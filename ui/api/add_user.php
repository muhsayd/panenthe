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

//Define Fields
$fields = array(
	"username"			=>	"",
	"password"			=>	"",
	"email"				=>	"",
	"first_name"		=>	"",
	"last_name"			=>	""
);

foreach($fields AS $name => $value){
	if(!isset(main::$params[$name])){
		die("no_".$name."_found");
	} else {
		$fields[$name] = main::$params[$name];
	}
}

//Start VO
$vo_users = new vo_users($fields);

//Set Password
$salt = substr(md5(time().rand(1,100)),0,12);
$vo_users->set_password(md5($fields['password'].$salt));
$vo_users->set_salt($salt);

//Set Properties
$vo_users->set_is_staff(0);
$vo_users->set_last_login(0);
$vo_users->set_last_refresh(0);
$vo_users->set_created(time());
$vo_users->set_modified(time());

//Do Insert
dao_users::insert($vo_users->insert_array());

echo dev::$db->lastInsertId();

require_once(dirname(__FILE__)."/api_end.php");

?>
