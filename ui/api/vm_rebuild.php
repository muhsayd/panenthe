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
require_once(SCRIPT_ROOT.'/lib/dao/dao_events.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_events.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_ip_pools.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_ip_pools.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_ost.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_ost.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_servers.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_servers.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/drivers/func/func_drivers.php');
require_once(SCRIPT_ROOT.'/apps/ost/func/func_ost.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_ip.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/vps_operations.php');
require_once(SCRIPT_ROOT.'/apps/events/func/event_api.php');

//Get VPS ID
if(!isset(main::$params['vps_id']) || empty(main::$params['vps_id'])){
	die("no_vps_id");
}

//Get VPS
$rows = func_vps::get_vps_by_id(main::$params['vps_id']);
if(!isset($rows[0])){
	die("no_vps_found");
}

//Get New Password
if(!isset(main::$params['passwd']) || empty(main::$params['passwd'])){
	die("no_password");
} else {
	$password = main::$params['passwd'];
}

//Get Ost
if(!isset(main::$params['ost_id']) || empty(main::$params['ost_id'])){
	die("no_ost_id");
} else {
	$ost_id = main::$params['ost_id'];
	$ost = func_ost::get_ost_by_id($ost_id);
	if(!isset($ost[0])){
		die("no_ost_found");
	} else {
		$ost = $ost[0];
	}
}

$vps = $rows[0];
$vo_vps =& $vps;

$vo_vps->set_ost($ost->get_ost_id());
$vo_vps->set_modified(time());
			
//Get IPS
$ips = func_vps_ip::get_all_ips($vo_vps);
$dns = func_vps_ip::get_dns($ips);

//Get VPS Driver
$background = true;
$vpsDriver = new vps_operations($vo_vps);
$vpsDriver->lock();
$vpsDriver->stop_vps($background,'lock');
$vpsDriver->rebuild_vps($background,'stop');
$vpsDriver->start_vps($background,'rebuild');
$vpsDriver->set_dns($dns,$background,'start');

//Add IPs
if(is_array($ips) && count($ips) > 0){
	$vpsDriver->add_ips($ips,$background,'start');
}

$vpsDriver->set_passwd('root',$password,$background,'start');
$vpsDriver->reboot_vps(true,'passwd');
$vpsDriver->unlock(array('start','rebuild','reboot'));
$vpsDriver->execute();

if($vpsDriver->isOkay()){
	//Update DB
	dao_vps::update(
		$vo_vps->update_rebuild_array(),
		" WHERE vps_id = :v_vps_id ",
		array("v_vps_id"=>$vo_vps->get_vps_id())
	);
	echo 0;
}
else
{
	echo 1;
	echo $vpsDriver->getOutput();
}

require_once(dirname(__FILE__)."/api_end.php");

?>
