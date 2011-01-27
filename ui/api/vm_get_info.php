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
require_once(SCRIPT_ROOT.'/lib/dao/dao_vps.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_vps.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_drivers.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_drivers.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/drivers/func/func_drivers.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_ip.php');

//Get VPS ID
if(!isset(main::$params['vps_id']) || empty(main::$params['vps_id'])){
	die("no_vps_id");
}

//Get VPS
$rows = func_vps::get_vps_by_id(main::$params['vps_id']);
if(!isset($rows[0])){
	die("no_vps_found");
}

$vps = $rows[0];
echo main::output_from_vo($vps);

//Get Driver
$rows = func_drivers::get_driver_by_id($vps->get_driver_id());
if(isset($rows[0])){
	$driver = $rows[0];
	echo "&driver=".$driver->get_ext_ref();
}

//Get IPS
$ips = func_vps_ip::get_all_ips($vps);
if(is_array($ips) && count($ips) > 0){
	foreach($ips AS $key => $ip){
		$inc = '&';
		echo $inc."ip[".$key."]=".$ip['ip'];
	}
}

require_once(dirname(__FILE__)."/api_end.php");

?>
