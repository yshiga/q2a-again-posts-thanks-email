<?php

class q2a_again_posts_thanks_email_db_client
{

  public static function getLastPostTimestamp($userid) {
    $sql = 'SELECT UNIX_TIMESTAMP(MAX( created)) as timestamp FROM  `qa_posts` WHERE userid =' . $userid;
		$result = qa_db_query_sub($sql);
		$arr = qa_db_read_all_assoc($result);
    return $arr[0]['timestamp'];
  }

  public static function getUserInfo($userid)
	{
		$sql = 'select email,handle from qa_users where userid=' . $userid;
		$result = qa_db_query_sub($sql);
		$users = qa_db_read_all_assoc($result);
    return $users[0];
	}
}
