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

//Get Server
$rows = func_users::get_users(
	"is_staff = :v_is_staff",
	array(
		"v_is_staff"	=>	0
	)
);

if(!isset($rows[0])){
	die("no_users_found");
}

foreach($rows AS $user){
	if(isset($inc)){
		$inc = "###";
	}
	else
	{
		$inc = "";
	}
	echo $inc;
	echo main::output_from_vo($user);
}

require_once(dirname(__FILE__)."/api_end.php");

?>
