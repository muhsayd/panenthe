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
require_once(SCRIPT_ROOT.'/lib/dao/dao_plans.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_plans.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/plans/func/func_plans.php');

//Define Fields
$fields = array(
	"name"			=>	"",
	"disk_space"	=>	"",
	"backup_space"	=>	"",
	"swap_space"	=>	"",
	"g_mem"			=>	"",
	"b_mem"			=>	"",
	"cpu_pct"		=>	"",
	"cpu_num"		=>	"",
	"out_bw"		=>	"",
	"in_bw"			=>	""
);

foreach($fields AS $name => $value){
	if(!isset(main::$params[$name])){
		die("no_".$name."_found");
	} else {
		$fields[$name] = main::$params[$name];
	}
}

$vo_plans = new vo_plans($fields);
$vo_plans->set_created(time());
$vo_plans->set_modified(time());
dao_plans::insert($vo_plans->insert_array());

echo dev::$db->lastInsertId();

require_once(dirname(__FILE__)."/api_end.php");

?>
