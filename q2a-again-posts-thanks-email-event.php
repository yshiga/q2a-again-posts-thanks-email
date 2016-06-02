<?php
if (!defined('QA_VERSION')) {
	require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../qa-include/qa-base.php';
}

require_once QA_INCLUDE_DIR.'app/emails.php';
require_once QA_PLUGIN_DIR . 'q2a-again-posts-thanks-email/q2a-again-posts-thanks-email-db-client.php';

class q2a_again_posts_thanks_email_event
{
	function process_event ($event, $userid, $handle, $cookieid, $params)
	{
		if (!($event == 'q_post' || $event == 'a_post' || $event == 'c_post'))
			return;

		$days = (int)qa_opt('q2a-again-posts-thanks-day');	// 閾値：日数
		$lastPosttimestamp = q2a_again_posts_thanks_email_db_client::getLastPostTimestamp($userid);

		if ($lastPosttimestamp < time() - $days * 24 * 60 * 60) {
			$user = q2a_again_posts_thanks_email_db_client::getUserInfo($userid);
			$handle = $user['handle'];
			$email = $user['email'];
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

}
