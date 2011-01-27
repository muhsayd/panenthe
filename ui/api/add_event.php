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

define("IN_API",true);
require_once(dirname(__FILE__)."/api_common.php");

//Load Libs
require_once(SCRIPT_ROOT.'/lib/dao/dao_events.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_events.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/events/func/event_api.php');

if(!isset(main::$params['event']) || main::$params['event'] == ''){
	die("no_event_specified");
}

event_api::add_event(main::$params['event']);

echo 0;

require_once(dirname(__FILE__)."/api_end.php");

?>
