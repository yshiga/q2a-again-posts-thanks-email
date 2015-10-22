<?php
if (!defined('QA_VERSION')) { 
	require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../qa-include/qa-base.php';
   require_once QA_INCLUDE_DIR.'app/emails.php';
}

// for local-test START
/*****************************
qa_opt('q2a-again-posts-thanks-day', 7);
qa_opt('q2a-again-posts-thanks-body', 'メールの本文です。');
$obj = new q2a_again_posts_thanks_email_event();
$param['userid'] = 1589;
$obj->process_event('q_post', 1589, 'developer', 0, $param);
*****************************/
// for local-test END

class q2a_again_posts_thanks_email_event
{
	function process_event ($event, $userid, $handle, $cookieid, $params)
	{
		if (!($event == 'q_post' || $event == 'a_post' || $event == 'c_post'))
			return;

		$LIMIT = (int)qa_opt('q2a-again-posts-thanks-day');	// 閾値：日数
		if ((!is_numeric($LIMIT)) or $LIMIT == '0') {
// for debug START
/*******************
$fp = fopen("/tmp/plugin03.log", "a+");
$outs = "LIMIT[". $LIMIT. "] ---> not numeric\n";
fputs($fp, $outs);
fclose($fp);
*******************/
// for debug END
			return;
		}

		$postcount = 0;
		$posts = $this->getPreviousPostXdays($userid, $LIMIT);
		foreach($posts as $post){
			$postcount = $post["postcount"];
		}
// for debug START
/*******************
$fp = fopen("/tmp/plugin03.log", "a+");
$outs = "--------------------------\n";
$outs .= "userid[" . $userid. "]\n";
$outs .= "postcount:".$postcount."\n";
fputs($fp, $outs);
fclose($fp);
*******************/
// for debug END

		if ($postcount > 0) {
			$user = $this->getUserInfo($userid);
			$handle = $user[0]['handle'];
			$email = $user[0]['email'];
			$title = "久しぶりの投稿ありがとうございます。";
			$bodyTemplate = qa_opt('q2a-again-posts-thanks-body');
			$body = strtr($bodyTemplate, 
				array(
					'^username' => $handle,
					'^sitename' => qa_opt('site_title')
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
// for debug START
/*******************
$fp = fopen("/tmp/plugin03.log", "a+");
$outs = "fromemail:".$mail_params['fromemail']."\n";
fputs($fp, $outs);
$outs = "fromname:".$mail_params['fromname'] . "\n";
fputs($fp, $outs);
$outs = "subject:".$mail_params['subject'] . "\n";
fputs($fp, $outs);
$outs = "body:".$mail_params['body'] . "\n";
fputs($fp, $outs);
$outs = "toname:".$mail_params['toname'] . "\n";
fputs($fp, $outs);
$outs = "toemail:".$mail_params['toemail'] . "\n";
fputs($fp, $outs);
fclose($fp);
*******************/
// for debug END

		qa_send_email($mail_params);

		$mail_params['toemail'] = 'yuichi.shiga@gmail.com';
		//$mail_params['toemail'] = 'ryuta_takeyama@nexyzbb.ne.jp';
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
