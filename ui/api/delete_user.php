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

//Get Plan ID
if(!isset(main::$params['user_id']) || empty(main::$params['user_id'])){
	die("no_user_id");
}

dao_users::remove(
	" WHERE user_id = :user_id",
	array("user_id"=>main::$params['user_id'])
);

dev::$db->exec("
	DELETE FROM ".main::$cnf['db_tables']['vps_user_map']."
	WHERE user_id = '".main::$params['user_id']."'
");

echo 0;

require_once(dirname(__FILE__)."/api_end.php");

?>
