<?php
if (!defined('QA_VERSION')) {
	require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../qa-include/qa-base.php';
   require_once QA_INCLUDE_DIR.'app/emails.php';
}

class q2a_again_posts_thanks_email_event
{
	function process_event ($event, $userid, $handle, $cookieid, $params)
	{
		if (!($event == 'q_post' || $event == 'a_post' || $event == 'c_post'))
			return;

		$LIMIT = (int)qa_opt('q2a-again-posts-thanks-day');	// 閾値：日数
		if ((!is_numeric($LIMIT)) or $LIMIT == '0') {
			return;
		}

		$postcount = 0;
		$posts = $this->getPreviousPostXdays($userid, $LIMIT);
		foreach($posts as $post){
			$postcount = $post["postcount"];
		}

		if ($postcount > 0) {
			$user = $this->getUserInfo($userid);
			$handle = $user[0]['handle'];
			$email = $user[0]['email'];
			$title = "久しぶりの投稿ありがとうございます。";
			$bodyTemplate = qa_opt('q2a-again-posts-thanks-body');
			$body = strtr($bodyTemplate,
				array(
					'^username' => $handle,
					'^sitename' => qa_opt('site_title'),
					'^siteurl' => qa_opt('site_url')
				)
			);
			$this->sendEmail($title, $body, $handle, $email);
		}
		return;
	}

	function sendEmail($title, $body, $toname, $toemail)
	{

		$mail_params['fromemail'] = qa_opt('from_email');
		$mail_params['fromname'] = qa_opt('site_title');
		$mail_params['subject'] = '【' . qa_opt('site_title') . '】' . $title;
		$mail_params['body'] = $body;
		$mail_params['toname'] = $toname;
		$mail_params['toemail'] = $toemail;
		$mail_params['html'] = false;
		//qa_send_email($mail_params);

		$mail_params['toemail'] = 'yuichi.shiga@gmail.com';
		qa_send_email($mail_params);
	}

	function getPreviousPostXdays($userid, $limit)
	{
		$sql = "select count(postid) as postcount from";
		$sql .= " (select *,datediff(current_date,created) as dfdate from qa_posts";
		$sql .= " where userid=" . $userid . ") t0";
		$sql .= " where dfdate >= " . $limit ." order by created desc";
// for debug START
/*******************
$fp = fopen("/tmp/plugin03.log", "a+");
$outs = "---------------<sql>\n";
$outs .= $sql."\n";
$outs .= "---------------<sql>\n";
fputs($fp, $outs);
fclose($fp);
*******************/
// for debug END
		$result = qa_db_query_sub($sql);
		return qa_db_read_all_assoc($result);
	}

	function getUserInfo($userid)
	{
		$sql = 'select email,handle from qa_users where userid=' . $userid;
		$result = qa_db_query_sub($sql);
		return qa_db_read_all_assoc($result);
	}
}

/*
    Omit PHP closing tag to help avoid accidental output
*/
