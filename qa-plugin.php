<?php
/*
	Plugin Name: again posts thanks mail
	Plugin URI: 
	Plugin Description: send thanks mail to users who long time no see over X days
	Plugin Version: 0.3
	Plugin Date: 2015-10-18
	Plugin Author:
	Plugin Author URI:
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.7
	Plugin Update Check URI: 
*/
if (!defined('QA_VERSION')) {
	header('Location: ../../');
	exit;
}

qa_register_plugin_module('module', 'q2a-again-posts-thanks-email-admin.php', 'q2a_again_posts_thanks_email_admin', 'again posts thanks admin');
qa_register_plugin_module('event', 'q2a-again-posts-thanks-email-event.php', 'q2a_again_posts_thanks_email_event', 'Again Posts Thanks');

/*
	Omit PHP closing tag to help avoid accidental output
*/
