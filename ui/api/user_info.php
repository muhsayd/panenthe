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
require_once(SCRIPT_ROOT.'/lib/dao/dao_users.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_users.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/users/func/func_users.php');

//Get User ID
if(!isset(main::$params['user_id']) || empty(main::$params['user_id'])){
	die("no_user_id");
}

//Get User
$rows = dao_users::select(
	"WHERE user_id = :user_id AND is_staff = 0",
	array("user_id"=>main::$params['user_id'])
);
if(!isset($rows[0])){
	die("no_user_found");
}

$user = $rows[0];
echo main::output_from_vo($user);

require_once(dirname(__FILE__)."/api_end.php");

?>
