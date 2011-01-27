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
require_once(SCRIPT_ROOT.'/lib/dao/dao_plans.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_plans.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_servers.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_servers.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_users.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_users.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/drivers/func/func_drivers.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_au.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_ip.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_user.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/vps_operations.php');
require_once(SCRIPT_ROOT.'/apps/events/func/event_api.php');
require_once(SCRIPT_ROOT.'/apps/users/func/func_users.php');

//Check Requirements
if(
	!isset(main::$params['server_id']) || 
	!isset(main::$params['driver_id']) ||
	!isset(main::$params['name']) ||
	!isset(main::$params['hostname']) || 
	!isset(main::$params['ost_id']) || 
	!isset(main::$params['plan_id']) || 
	!isset(main::$params['user_id']) || 
	!isset(main::$params['no_ips'])
){
	die("missing_requirements");
}

//Check for Send Mail
if(isset(main::$params['send_email'])){
	$send_email = true;
} else {
	$send_email = false;
}

//Check for Real ID
if(isset(main::$params['real_id'])){
	$real_id = main::$params['real_id'];
} else {
	$real_id = false;
}

//Get Server
$vo_server = dao_servers::select(
	"WHERE server_id = :v_server_id",
	array("v_server_id"	=>	main::$params['server_id'])
);
if(count($vo_server) == 0){
	die("server_not_found");
}
$vo_server = $vo_server[0];

//Get Driver
$vo_driver = dao_drivers::select(
	"WHERE driver_id = :v_driver_id",
	array("v_driver_id"	=>	main::$params['driver_id'])
);
if(count($vo_driver) == 0){
	die("driver_not_found");
}
$vo_driver = $vo_driver[0];

//Get OST
$vo_ost = dao_ost::select(
	"WHERE ost_id = :v_ost_id",
	array("v_ost_id"	=>	main::$params['ost_id'])
);
if(count($vo_ost) == 0){
	die("ost_not_found");
}
$vo_ost = $vo_ost[0];

//Get Plan
$vo_plan = dao_plans::select(
	"WHERE plan_id = :v_plan_id",
	array("v_plan_id"	=>	main::$params['plan_id'])
);
if(count($vo_plan) == 0){
	die("plan_not_found");
}
$vo_plan = $vo_plan[0];

//Get User
$vo_user = dao_users::select(
	"WHERE user_id = :v_user_id AND is_staff = 0",
	array("v_user_id"	=>	main::$params['user_id'])
);
if(count($vo_user) == 0){
	die("user_not_found");
}
$vo_user = $vo_user[0];

//Setup VM Data
$vo_vps = new vo_vps();
$vo_vps->set_server_id($vo_server->get_server_id());
$vo_vps->set_driver_id($vo_driver->get_driver_id());
$vo_vps->set_name(main::$params['name']);
$vo_vps->set_hostname(main::$params['hostname']);

if($real_id !== false){
	$vo_vps->set_real_id($real_id);
}

$vo_vps->set_disk_space($vo_plan->get_disk_space());
$vo_vps->set_backup_space($vo_plan->get_backup_space());
$vo_vps->set_swap_space($vo_plan->get_swap_space());
$vo_vps->set_g_mem($vo_plan->get_g_mem());
$vo_vps->set_b_mem($vo_plan->get_b_mem());
$vo_vps->set_cpu_pct($vo_plan->get_cpu_pct());
$vo_vps->set_cpu_num($vo_plan->get_cpu_num());
$vo_vps->set_in_bw($vo_plan->get_in_bw());
$vo_vps->set_out_bw($vo_plan->get_out_bw());
$vo_vps->set_ost($vo_ost->get_ost_id());
$vo_vps->set_is_running(0);
$vo_vps->set_is_suspended(0);
$vo_vps->set_is_locked(0);
$vo_vps->set_created(time());
$vo_vps->set_modified(time());

/* TODO THIS REALLY NEEDS DONE but goddamn
$query = dev::$db->query("
	SELECT * FROM ".main::$cnf['db_tables']['vps']."
");

$total_vms = $query->rowCount();

//Check Limits
if($total_vms > main::$cnf['vm_limit']){
	main::error_page("This VM cannot be created as the licenses does not allow for any more Virtual Machines.");
}
*/
	
//Insert VPS
dao_vps::insert($vo_vps->insert_array());
$vo_vps->set_vps_id(dev::$db->lastInsertId());
	
//Get VPS Real Id
if($real_id === false){
	$vpsCreate = new vps_operations($vo_vps);
	$_real_id = $vpsCreate->next_id();
}
	
//Get New VO for the VM
$rows = func_vps::get_vps_by_id($vo_vps->get_vps_id());
$vo_vps = $rows[0];
if(main::$cnf['ui_config']['vps_debug'] == "true"){
	dev::output_r($vo_vps);
}
	
//Map Ips
list($ips_map,$dns) = func_vps_ip::map_ips(
	$vo_vps->get_vps_id(),
	$vo_vps->get_server_id(),
	main::$params['no_ips'],
	'',
	true
);
	
//Get VPS Driver
event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was queued for creation.');
$vpsDriver = new vps_operations($vo_vps);
$vpsDriver->lock();
$vpsDriver->create_vps(true,'lock');
	
//Save VPS Event
list($password,$vo_user) = func_vps_user::add_vps_user(
	array(
		"user_add_type"	=>	"assign_user",
		"user_id"		=>	$vo_user->get_user_id()
	),
	$vo_vps,
	true
);

$gen_root_pass = true;
$root_password = substr(md5(time().rand(1,100)),0,12);
	
//Generate Confirmation Info
func_vps::generate_confirmation_info(
	$vo_user,
	$vo_vps,
	$ips_map,
	$password,
	$root_password
);
	
//Send Welcome Email
if($send_email){
	func_vps::welcome_email();
}

//Get Updated VO
$rows = dao_vps::select(
	"WHERE vps_id = :v_vps_id",
	array("v_vps_id"=>$vo_vps->get_vps_id())
);

$vo_vps = $rows[0];
$vpsDriver->start_vps(true,'create');
if(!empty($dns)){
	$vpsDriver->set_dns($dns,true,'start');
}
$vpsDriver->add_ips($ips_map,true,'start');
$vpsDriver->set_passwd('root',$root_password,true,'start');
$vpsDriver->unlock(array('set_dns','add_ip','passwd'));
if($real_id === false){
	$vpsDriver->execute();
}

if($vpsDriver->isOkay() || $real_id !== false){

	func_vps_ip::insert_mapped_ips($ips_map,$vo_vps->get_vps_id());

	$success = $vo_vps->get_vps_id().'::'.$root_password;

} else {

	//Roll Back DB
	dao_vps::remove(
		" WHERE vps_id = :vps_id ",
		array("vps_id"=>$vo_vps->get_vps_id())
	);

	$success = false;

}

if($success !== false){
	echo $success;
} else {
	echo "creation_failed";
}

require_once(dirname(__FILE__)."/api_end.php");

?>
