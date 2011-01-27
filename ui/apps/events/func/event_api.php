<?php
/**
 * Panenthe VPS Management
 *
 * This is NOT Free Software
 * This software is NOT Open Source.
 * Please see panenthe.com for more information.
 *
 * Use of this software is binding of a license agreement.
 * This license agreeement may be found at panenthe.com
 *
 * Panenthe DOES NOT offer this software with any WARRANTY whatsoever.
 * Panenthe DOES NOT offer this software with any GUARANTEE whatsoever.
 *
 * @copyright Panenthe, Nullivex LLC. All Rights Reserved.
 * @author Nullivex LLC <contact@nullivex.com>
 * @license http://www.panenthe.com
 * @link http://www.panenthe.com
 *
 */

if(!defined("IS_INCLUDED")){
	exit;
}

require_once(main::$root.'/lib/dao/dao_events.php');
require_once(main::$root.'/lib/vo/vo_events.php');

class event_api {

	public static function add_event($message){

		$vo_events = new vo_events(array(
			"message"			=>	$message,
			"code"				=>	"0000",
			"time"				=>	time(),
			"is_acknowledged"	=>	"0",
			"created"			=>	time(),
			"modified"			=>	time()
		));

		dao_events::insert($vo_events->insert_array());

		return true;
		
	}

}

?>
