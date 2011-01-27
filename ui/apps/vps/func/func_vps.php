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

class func_vps {

	static $create_confirm = false;
	static $confirmation_info = false;

	public static function get_vm_action_message(&$vpsDriver,$success_message){
		if(!is_object($vpsDriver)){
			return false;
		}
		return $vpsDriver->getOutput() .' '.$success_message;
	}

	public static function get_vps($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_vps::select($where." ORDER BY vps_id DESC ".$limit,$where_values);
	}

	public static function get_vps_with_ost($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_vps::select_with_ost($where." ORDER BY vps_id DESC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array()){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_vps::select($where,$where_values));
	}

	public static function get_vps_by_id($vps_id){

		return dao_vps::select(
			"WHERE vps_id = :vps_id",
			array("vps_id"=>$vps_id)
		);

	}

	public static function get_by_ost_id($ost_id){

		return dao_vps::select(
			"WHERE ost = :ost",
			array("ost"=>$ost_id)
		);

	}

	public static function insert_status_history($vo_vps,$status){

		$query = dev::$db->prepare("
			INSERT INTO ".main::$cnf['db_tables']['vps_status_history']."
			(
				vps_id,
				message,
				time,
				created,
				modified
			)
			VALUES
			(
				:v_vps_id,
				:v_message,
				:v_time,
				:v_created,
				:v_modified
			)
		");

		$chk = $query->execute(
			array(
				"v_vps_id"	=>	$vo_vps->get_vps_id(),
				"v_message"	=>	$status['code'],
				"v_time"	=>	time(),
				"v_created"	=>	time(),
				"v_modified"=>	time()
			)
		);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		return dev::$db->lastInsertId();

	}

	public static function get_status_history($vo_vps){

		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['vps_status_history']."
			WHERE vps_id = '".$vo_vps->get_vps_id()."'
			ORDER BY vps_status_history_id DESC
			LIMIT 1
		");

		if($query->rowCount() > 0){
			$row = $query->fetch();
			return $row['message'];
		}
		else
		{
			return false;
		}

	}

	public static function get_stats($vo_vps,$status='0901'){

		$query = dev::$db->prepare("
			SELECT * FROM ".main::$cnf['db_tables']['vps_stats']."
			WHERE vps_id = :v_vps_id
		");

		$query->execute(array("v_vps_id"=>$vo_vps->get_vps_id()));

		$rows = array(
			"load_average_1"	=>	"0",
			"load_average_5"	=>	"0",
			"load_average_15"	=>	"0",
			"usage_mem"			=>	"0",
			"usage_disk"		=>	"0",
			"usage_backup"		=>	"0",
			"in_bw"				=>	"0",
			"out_bw"			=>	"0",
			"uptime"			=>	"0"
		);
		foreach($query->fetchAll() AS $result){
			$rows[$result['name']] = $result['value'];
		}

		$limits = array(
			"g_mem"			=>	self::hr_size($vo_vps->get_g_mem()),
			"disk_space"	=>	self::hr_size($vo_vps->get_disk_space()),
			"backup_space"	=>	self::hr_size($vo_vps->get_backup_space()),
			"swap_space"	=>	self::hr_size($vo_vps->get_swap_space()),
			"in_bw"			=>	self::hr_size($vo_vps->get_in_bw()),
			"out_bw"		=>	self::hr_size($vo_vps->get_out_bw())
		);

		if(main::$cnf['ui_config']['vps_debug'] == 'true'){
			dev::output_r($rows);
		}

		if($status == '0901'){
			$raw_stats = array(
				"memory_usage" 	=>	$rows['usage_mem'],
				"disk_usage"	=>	$rows['usage_disk']

			);
		}
		else
		{
			$raw_stats = array(
				"memory_usage" 	=>	0,
				"disk_usage"	=>	0
			);
		}

		$raw_stats = array_merge(
			$raw_stats,
			array(
				"backup_usage"	=>	$rows['usage_backup'],
				"in_bw"			=>	ceil($rows['in_bw'] / 1024),
				"out_bw"		=>	ceil($rows['out_bw'] / 1024)
			)
		);

		$stats = array(
			"memory_usage"	=>	self::hr_size($raw_stats['memory_usage'],$limits['g_mem'][1]),
			"disk_usage"	=>	self::hr_size($raw_stats['disk_usage'],$limits['disk_space'][1]),
			"backup_usage"	=>	self::hr_size(0,$limits['backup_space'][1]),
			"in_bw_usage"	=>	self::hr_size($raw_stats['in_bw'],$limits['in_bw'][1]),
			"out_bw_usage"	=>	self::hr_size($raw_stats['out_bw'],$limits['out_bw'][1])
		);

		//Setup Percents
		if($vo_vps->get_g_mem() > 0){
			$stats['memory_pct'] = number_format(
				(($raw_stats['memory_usage'] / $vo_vps->get_g_mem()) * 100),
				0
			);
		}
		else
		{
			$stats['memory_pct'] = 0;
		}

		if($vo_vps->get_disk_space() > 0){
			$stats['disk_pct'] = number_format(
				(($raw_stats['disk_usage'] / $vo_vps->get_disk_space()) * 100),
				0
			);
		}
		else
		{
			$stats['disk_pct'] = 0;
		}

		if($vo_vps->get_backup_space() > 0){
			$stats['backup_pct'] = number_format(
				(($raw_stats['backup_usage'] / $vo_vps->get_backup_space()) * 100),
				0
			);
		}
		else
		{
			$stats['backup_pct'] = 0;
		}

		if($vo_vps->get_in_bw() > 0){
			$stats['in_bw_pct'] = number_format(
				(($raw_stats['in_bw'] / $vo_vps->get_in_bw()) * 100),
				0
			);
		}
		else
		{
			$stats['in_bw_pct'] = 0;
		}

		if($vo_vps->get_out_bw() > 0){
			$stats['out_bw_pct'] = number_format(
				(($raw_stats['out_bw'] / $vo_vps->get_out_bw()) * 100),
				0
			);
		}
		else
		{
			$stats['out_bw_pct'] = 0;
		}

		//Setup Remaining Percents
		$stats['memory_rep'] = 100 - $stats['memory_pct'];
		$stats['disk_rep'] = 100 - $stats['disk_pct'];
		$stats['backup_rep'] = 100 - $stats['backup_pct'];
		$stats['out_bw_rep'] = 100 - $stats['out_bw_pct'];
		$stats['in_bw_rep'] = 100 - $stats['in_bw_pct'];

		//Add Load Avgs
		$stats['load_average_1'] = $rows['load_average_1'];
		$stats['load_average_5'] = $rows['load_average_5'];
		$stats['load_average_15'] = $rows['load_average_15'];
		$stats['uptime'] = $rows['uptime'];

		return array($stats,$limits);

	}

	public static function hr_size($size,$override=false){

		$den = array(
			"kb"	=>	1,
			"mb"	=>	1024,
			"gb"	=>	1024 * 1024,
			"tb"	=>	1024 * 1024 * 1024
		);

		if($override !== false && array_key_exists($override,$den)){
			$hr_size = round($size / $den[$override],2);
			return array($hr_size,$override);
		}

		if($size > $den['gb'] * 1000){
			$hr_size = round($size / $den['tb'],2);
			return array($hr_size,'tb');
		}
		else
		if($size > $den['mb'] * 1000){
			$hr_size = round($size / $den['gb'],2);
			return array($hr_size,'gb');
		}
		else
		if($size > $den['kb'] * 1000){
			$hr_size = round($size / $den['mb'],2);
			return array($hr_size,'mb');
		}
		else
		{
			return array(round($size,2),'kb');
		}

	}


	public static function get_stats_beancounts($vo_vps){

		$query = dev::$db->prepare("
			SELECT * FROM ".main::$cnf['db_tables']['vps_stats']."
			WHERE vps_id = :v_vps_id
		");

		$query->execute(array("v_vps_id"=>$vo_vps->get_vps_id()));

		$rows = array();
		foreach($query->fetchAll() AS $result){
			$rows[$result['name']] = $result['value'];
		}

		if(isset($rows['usage_beancounters'])){
			$beancounts = $rows['usage_beancounters'];
			$beancounts = explode("\n",$beancounts);
		} else {
			$beancounts = array();
		}

		$beancounters = array();
		$i = 0;
		foreach($beancounts AS $key => $line){
			if(empty($line)){
				unset($beancounts[$key]);
				continue;
			}
			preg_match(
				'/([a-z]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s+([0-9]+)\s*$/i',
				$line,
				$matches
			);
			if(count($matches) == 7){
				$beancounters[$i]['resource'] = trim($matches[1]);
				$beancounters[$i]['held'] = trim($matches[2]);
				$beancounters[$i]['maxheld'] = trim($matches[3]);
				$beancounters[$i]['barrier'] = trim($matches[4]);
				$beancounters[$i]['limit'] = trim($matches[5]);
				$beancounters[$i]['failcnt'] = trim($matches[6]);
			}
			$i++;
		}

		return $beancounters;

	}

	public static function get_drivers($name,$value){

		$rows = dao_drivers::select('');
		$options = '';
		foreach($rows AS $result){

			if($result->get_driver_id() == $value){
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}

			$options .= dev::$tpl->parse(
				'global',
				'select_row',
				array(
				"selected"  =>	$selected,
				"value"	    =>	$result->get_driver_id(),
				"name"	    =>	$result->get_name()
				),
				true
			);

		}

		$driver_id = dev::$tpl->parse(
			'global',
			'select',
			array(
				"name"	=>	$name,
				"options"	=>	$options
			),
			true
		);

		return $driver_id;

	}

	public static function get_ost($name,$value,$server=false){

		if(!$server){
			$rows = dao_ost::select_with_driver('');
		} else {

			$rows = dao_ost::select_with_driver(
				"WHERE o.driver_id = :v_driver_id",
				array(
					"v_driver_id"	=>	$server->get_driver_id()
				)
			);

		}

		$options = '';
		foreach($rows AS $result){

			if($result->get_ost_id() == $value){
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}

			$options .= dev::$tpl->parse(
				'global',
				'select_row',
				array(
				"selected"	=>	$selected,
				"value"		=>	$result->get_ost_id(),
				"name"		=>	'('.$result->get_driver_id().') '.$result->get_name()
				),
				true
			);

		}

		$driver_id = dev::$tpl->parse(
			'global',
			'select',
			array(
				"name"	=>	$name,
				"options"	=>	$options
			),
			true
		);

		return $driver_id;

	}

	public static function get_servers($name,$value){

		$rows = dao_servers::select('');
		$options = '';
		foreach($rows AS $result){

			if($result->get_parent_server_id() != 0){
				if($result->get_server_id() == $value){
					$selected = ' selected="selected"';
				}
				else
				{
					$selected = '';
				}

				if($result->get_driver_id() == null){
					$result->set_driver_id(func_vps::set_server_driver_id($result));
				}

				$driver = dao_drivers::get_by_driver_id($result->get_driver_id());

				if(is_object($driver) && $result->get_driver_id() !== false){
					$driver_name = $driver->get_name();
				} else {
					$driver_name = 'Unknown';
				}

				$options .= dev::$tpl->parse(
					'global',
					'select_row',
					array(
						"selected"  =>	$selected,
						"value"	    =>	$result->get_server_id(),
						"name"	    =>	'('.$driver_name.') '.$result->get_name()
					),
					true
				);
			}

		}

		$driver_id = dev::$tpl->parse(
			'global',
			'select',
			array(
				"name"	=>	$name,
				"options"	=>	$options
			),
			true
		);

		return $driver_id;

	}

	public static function set_server_driver_id($server){

		$rows = dao_drivers::select('');

		//Get Error Code
		$error = main::get_err_code("DRIVER_STATUS_STARTED");

		$driver_found = false;
		foreach($rows AS $driver){

			$ops = new server_operations($server);
			$ops->driver_status($driver);
			$ops->execute();

			if(in_array($error['code'],$ops->get_codes())){
				$driver_found = true;
				break;
			}

		}

		if(!$driver_found){
			return false;
		} else {

			$server->set_driver_id($driver->get_driver_id());
			dao_servers::update(
				$server->update_array(),
				"WHERE server_id = :v_server_id",
				array(
					"v_server_id"	=>	$server->get_server_id()
				)
			);
			return true;

		}

		return null;

	}

	public static function get_server_by_id($server_id){

		return dao_servers::select(
			"WHERE server_id = :server_id",
			array("server_id"=>$server_id)
		);

	}

	public static function get_plans($name,$value){

		$rows = array();
		$rows[] = new vo_plans(array("name"=>"Custom"));
		$db_rows = dao_plans::select('');
		$rows = array_merge($rows,$db_rows);

		$options = '';
		foreach($rows AS $result){

			if($result->get_plan_id() == $value){
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}

			$options .= dev::$tpl->parse(
				'global',
				'select_row',
				array(
				"selected"  =>	$selected,
				"value"	    =>	$result->get_plan_id(),
				"name"	    =>	$result->get_name()
				),
				true
			);

		}

		$plans = dev::$tpl->parse(
			'global',
			'select',
			array(
				"name"		=>	$name,
				"options"	=>	$options
			),
			true
		);

		return $plans;
	}

	public static function get_plan_information(){

		$plan_information = '';
		$rows = dao_plans::select('');

		foreach($rows AS $result){
			$plan_information .= dev::$tpl->parse(
				'vps',
				'plan_information_row',
				array(
					"plan_id"		=>	$result->get_plan_id(),
					"disk_space"	=>	$result->get_disk_space(),
					"swap_space"	=>	$result->get_swap_space(),
					"backup_space"	=>	$result->get_backup_space(),
					"g_mem"			=>	$result->get_g_mem(),
					"b_mem"			=>	$result->get_b_mem(),
					"cpu_pct"		=>	$result->get_cpu_pct(),
					"cpu_num"		=>	$result->get_cpu_num(),
					"out_bw"		=>	$result->get_out_bw(),
					"in_bw"			=>	$result->get_in_bw()
				),
				true
			);
		}

		return $plan_information;

	}

	public static function change_limits(){

		if(!main::$is_staff){
			main::error_page("You cannot preform this action.");
		}

		if(isset(dev::$post['vps_id'])){

			$rows = dao_vps::select(
				"WHERE vps_id = :v_vps_id",
				array("v_vps_id"=>dev::$post['vps_id'])
			);

			if(isset($rows[0])){
				$vo_vps = $rows[0];
			}
			else
			{
				echo "VM not found.";
				exit;
			}

			//Get the Old Data
			$rows = dao_vps::select(
				"WHERE vps_id = :v_vps_id",
				array("v_vps_id"=>dev::$post['vps_id'])
			);
			$old_vo = $rows[0];

			$vo_vps->populate(dev::$post);
			$vo_vps->set_modified(time());

			//Update DB
			dao_vps::update(
				$vo_vps->update_limits_array(),
				" WHERE vps_id = :v_vps_id ",
				array("v_vps_id"=>$vo_vps->get_vps_id())
			);

			//Get New VO
			$rows = func_vps::get_vps_by_id($vo_vps->get_vps_id());
			$vo_vps = $rows[0];
			if(main::$cnf['ui_config']['vps_debug'] == "true"){
				dev::output_r($vo_vps);
			}

			//Get VPS Driver
			$vpsDriver = new vps_operations($vo_vps);
			$vpsDriver->mod_vps($old_vo);
			$vpsDriver->execute();

			if($vpsDriver->isOkay()){

				event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" limits were updated.');
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM Limits have been updated!"));
			}
			else
			{
				event_api::add_event($vpsDriver->getOutput());
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM Limits change has failed!"));
			}

		}

	}

	public static function change_rebuild(){

		if(isset(dev::$post['vps_id'])){

			if(dev::$post['root_password'] != dev::$post['confirm_root_password']){
				main::set_action_message("Root passwords did not match.");
				return;
			}

			$rows = dao_vps::select(
				"WHERE vps_id = :v_vps_id",
				array("v_vps_id"=>dev::$post['vps_id'])
			);
			if(isset($rows[0])){
				$vo_vps = $rows[0];
			}
			else
			{
				main::error_page("VM not found.");
				exit;
			}
			$vo_vps->populate(dev::$post);
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
			$vpsDriver->set_dns($dns,$background,'rebuild');

			//Add IPs
			if(is_array($ips) && count($ips) > 0){
				$vpsDriver->add_ips($ips,$background,'rebuild');
			}

			$vpsDriver->set_passwd('root',dev::$post['root_password'],$background,'rebuild');
			$vpsDriver->start_vps($background,'rebuild');
			$vpsDriver->unlock(array('start','rebuild','reboot'));
			$vpsDriver->execute();

			if($vpsDriver->isOkay()){
				//Update DB
				dao_vps::update(
					$vo_vps->update_rebuild_array(),
					" WHERE vps_id = :v_vps_id ",
					array("v_vps_id"=>$vo_vps->get_vps_id())
				);
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM has been queued to be rebuilt, check the event log for more!"));
			}
			else
			{
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM rebuild has failed!"));
			}

		}

	}

	public static function create_ost(){

		if(isset(dev::$post['vps_id'])){

			if(dev::$post['name_ext'] == ''){
				main::set_action_message("OST Name extension required.");
				return;
			}

			$rows = dao_vps::select(
				"WHERE vps_id = :v_vps_id",
				array("v_vps_id"=>dev::$post['vps_id'])
			);

			if(isset($rows[0])){
				$vo_vps = $rows[0];
			}
			else
			{
				echo "VM not found.";
				exit;
			}

			//Setup OST Vars
			$ost_name = dev::$post['name'].' - '.dev::$post['name_ext'];
			$ost_driver_id = dev::$post['driver_id'];
			$ost_arch = dev::$post['arch'];

			//Create OST File Name
			$ost_file = strtolower(preg_replace('/\W/','-',$ost_name));
			$ost_file = preg_replace('/[-]+/','-',$ost_file);

			//Event
			event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" queued for OS Template creation.');

			//Get VPS Driver
			$vpsDriver = new vps_operations($vo_vps);
			$vpsDriver->lock();
			$vpsDriver->create_ost(
				$ost_name,
				$ost_driver_id,
				$ost_arch,
				$ost_file,
				true,
				'lock'
			);
			$vpsDriver->unlock('create_ost');
			$vpsDriver->execute();

			if($vpsDriver->isOkay()){
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM OS Template has been queued for creation, check the event log for the status!"));
			}
			else
			{
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM OS Template creation has failed!"));
			}

		}

	}

	public static function change_name(){

		if(isset(dev::$post['vps_id']) && verify_vps::name()){
			$rows = dao_vps::select(
				"WHERE vps_id = :v_vps_id",
				array("v_vps_id"=>dev::$post['vps_id'])
			);
			if(isset($rows[0])){
				$vo_vps = $rows[0];
			}
			else
			{
				echo "VM not found.";
				exit;
			}
			$vo_vps->populate(dev::$post);
			$vo_vps->set_modified(time());

			$vpsDriver = new vps_operations($vo_vps);
			$vpsDriver->set_hostname($vo_vps->get_hostname());
			$vpsDriver->execute();

			if($vpsDriver->isOkay()){

				//Update DB
				dao_vps::update(
					$vo_vps->update_name_array(),
					" WHERE vps_id = :v_vps_id ",
					array("v_vps_id"=>$vo_vps->get_vps_id())
				);

				main::set_action_message(
					self::get_vm_action_message(
						$vpsDriver,
						"VM hostname/name has been changed!"
					)
				);

			}
			else
			{
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM hostname/name change has failed!"));
			}

		}

	}

	public static function suspension($vo_vps,$suspension='0'){

		$vo_vps->set_is_suspended($suspension);
		$vo_vps->set_modified(time());

		//Update DB
		dao_vps::update(
			$vo_vps->update_suspension_array(),
			" WHERE vps_id = :v_vps_id ",
			array("v_vps_id"=>$vo_vps->get_vps_id())
		);

	}

	public static function change_root_password(){

		if(isset(dev::$post['vps_id']) && verify_vps::root_password()){
			$rows = dao_vps::select(
				"WHERE vps_id = :v_vps_id",
				array("v_vps_id"=>dev::$post['vps_id'])
			);
			if(isset($rows[0])){
				$vo_vps = $rows[0];
			}
			else
			{
				echo "VM not found.";
				exit;
			}
			$vo_vps->populate(dev::$post);
			$vo_vps->set_modified(time());

			if(dev::$post['root_password'] == ""){
				$gen_root_pass = true;
				dev::$post['root_password'] = substr(md5(time().rand(1,100)),0,12);
			}
			else
			{
				$gen_root_pass = false;
			}

			//Get VPS Driver
			$vpsDriver = new vps_operations($vo_vps);
			$vpsDriver->set_passwd('root',dev::$post['root_password']);
			$vpsDriver->execute();

			if($vpsDriver->isOkay()){
				if($gen_root_pass){
					main::set_action_message(
						self::get_vm_action_message(
							$vpsDriver,
							"VM root password has been changed!
							Root Password generated is: <span style='color: black;'>".dev::$post['root_password']."</span>"
						)
					);
				}
				else
				{
					main::set_action_message(
						self::get_vm_action_message(
							$vpsDriver,
							"VM root password has been changed!"
						)
					);
				}
			}
			else
			{
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM root password change has failed!"));
			}

		}

	}

	public static function create_post_vps(){

		if(!main::$is_staff){
			main::error_page("You cannot preform this action.");
		}

		if(isset(dev::$post["vps_id"]) && verify_vps::insert()){
			$vo_vps = new vo_vps(dev::$post);
			$vo_vps->set_modified(time());
			if(empty(dev::$post["vps_id"])){

				$query = dev::$db->query("
					SELECT * FROM ".main::$cnf['db_tables']['vps']."
				");

				$total_vms = $query->rowCount();

				//Check Limits
				if($total_vms >= main::$cnf['vm_limit']){
					main::error_page(
						'This VM cannot be created as the licenses does not '.
						'allow for any more Virtual Machines.'
					);
				}

				//Save VPS
				$vo_vps->set_created(time());
				$vo_vps->set_is_locked(0);

				//Insert VPS
				dao_vps::insert($vo_vps->insert_array());
				$vo_vps->set_vps_id(dev::$db->lastInsertId());

				//Get VPS Real Id
				event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" queued for creation.');
				$vpsCreate = new vps_operations($vo_vps);
				$real_id = $vpsCreate->next_id();

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
					dev::$post['no_ips'],
					dev::$post['manual_ips'],
					true
				);

				//Get VPS Driver
				$vpsDriver = new vps_operations($vo_vps);
				$vpsDriver->lock();
				$vpsDriver->create_vps(true,'lock');

				//Save VPS Event
				list($password,$vo_user) = func_vps_user::add_vps_user(dev::$post,$vo_vps,true);

				if(dev::$post['root_password'] == ""){
					$gen_root_pass = true;
					dev::$post['root_password'] = substr(md5(time().rand(1,100)),0,12);
				}
				else
				{
					$gen_root_pass = false;
				}

				//Generate Confirmation Info
				func_vps::generate_confirmation_info($vo_user,$vo_vps,$ips_map,$password,dev::$post['root_password']);

				//Send Welcome Email
				if(isset(dev::$post['welcome_email'])){
					func_vps::welcome_email();
				}

				//$vpsDriver->start_vps(true,'create');
				if(!empty($dns)){
					$vpsDriver->set_dns($dns,true,'create');
				}
				$vpsDriver->add_ips($ips_map,true,'create');
				$vpsDriver->set_passwd('root',dev::$post['root_password'],true,'create');
				$vpsDriver->start_vps(true,'passwd');
				$vpsDriver->unlock(array('set_dns','add_ip','passwd','start'));
				$vpsDriver->execute();

				if($vpsDriver->isOkay()){

					func_vps_ip::insert_mapped_ips($ips_map,$vo_vps->get_vps_id());

					main::set_action_message(self::get_vm_action_message($vpsDriver,"VM creation has started!"));

					self::$create_confirm = true;
					self::create_confirmation();

				}
				else
				{

					//Roll Back DB
					dao_vps::remove(
						" WHERE vps_id = :vps_id ",
						array("vps_id"=>$vo_vps->get_vps_id())
					);

					main::set_action_message(self::get_vm_action_message($vpsDriver,"VM creation has failed!!"));

					self::$create_confirm = true;
					self::create_confirmation();

				}

			}
		}
	}

	public static function resend_welcome_email($vo_vps){

		$users = dev::$db->query("
			SELECT * FROM vps_user_map
			WHERE vps_id = '".$vo_vps->get_vps_id()."'
		");

		foreach($users->fetchAll() AS $result){
			$vo_user = func_users::get_user_by_id($result['user_id']);
			$vo_user = $vo_user[0];

			$ips_map = func_vps_ip::get_all_ips($vo_vps);

			self::generate_confirmation_info(
				$vo_user,
				$vo_vps,
				$ips_map,
				'(hidden)',
				'(hidden)'
			);

			$message = func_vps::parse_welcome_email(self::$confirmation_info);

			dev::$mail->sendMail(array(
				"To"		=>	self::$confirmation_info['email'],
				"Subject"	=>	"Welcome: ".self::$confirmation_info['hostname']." has been created!",
				"Message"	=>	$message
			));
		}

	}

	public static function generate_confirmation_info($vo_user,$vo_vps,$ips_map,$password,$root_password){

		if($vo_user !== false){
			$ip_address = 'No IP Address.';
			if(is_array($ips_map)){
				foreach($ips_map AS $ip){
					$ip_address = $ip['ip'];
					break;
				}
			}

			 //Get OST
			$ost = dao_ost::select(
				' WHERE ost_id = :v_ost_id ',
				array(
					"v_ost_id"	    =>	$vo_vps->get_ost()
				)
			);

			if(isset($ost[0])){
				$ost = $ost[0];
				$ost = $ost->get_name();
			}
			else
			{
				$ost = 'No OS Template Defined';
			}

			if($password === false){
				$password = '(hidden)';
			}

			self::$confirmation_info = array(
				"site_url"		=>	main::$cnf['login_url'],
				"site_name"		=>	main::$cnf['site_name'],
				"hostname"		=>	$vo_vps->get_hostname(),
				"name"			=>	$vo_vps->get_name(),
				"first_name"	=>	$vo_user->get_first_name(),
				"last_name"		=>	$vo_user->get_last_name(),
				"username"		=>	$vo_user->get_username(),
				"email"			=>	$vo_user->get_email(),
				"password"		=>	$password,
				"ip_address"	=>	$ip_address,
				"root_password"	=>	$root_password,
				"disk_space"	=>	floor(($vo_vps->get_disk_space() / 1024)),
				"backup_space"	=>	floor(($vo_vps->get_backup_space() / 1024)),
				"swap_space"	=>	floor(($vo_vps->get_swap_space() / 1024)),
				"g_mem"			=>	floor(($vo_vps->get_g_mem() / 1024)),
				"b_mem"			=>	floor(($vo_vps->get_b_mem() / 1024)),
				"cpu_pct"		=>	$vo_vps->get_cpu_pct(),
				"cpu_num"		=>	$vo_vps->get_cpu_num(),
				"out_bw"		=>	floor(($vo_vps->get_out_bw() / 1024 / 1024)),
				"in_bw"			=>	floor(($vo_vps->get_in_bw() / 1024 / 1024)),
				"ost"			=>	$ost
			);

		}

	}

	public static function welcome_email(){

		if(self::$confirmation_info !== false){
			$message = func_vps::parse_welcome_email(self::$confirmation_info);

			dev::$mail->sendMail(array(
				"To"		=>	self::$confirmation_info['email'],
				"Subject"	=>	"Welcome: ".self::$confirmation_info['hostname']." has been created!",
				"Message"	=>	$message
			));
		}

	}

	public static function create_confirmation(){

		dev::$tpl->parse(
			'vps',
			'confirmation',
			self::$confirmation_info
		);

	}

	public static function parse_welcome_email($tags){

		//Get Template
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['data']."
			WHERE name = 'welcome_email'
			ORDER BY data_id DESC
			LIMIT 1
		");

		if($query->rowCount() > 0){
			$row = $query->fetch();
			$tpl = $row['value'];

			foreach($tags AS $tag => $data){
				$tpl = str_ireplace('[['.$tag.']]',$data,$tpl);
			}

			return $tpl;

		}
		else
		{
			return '';
		}

	}

	public static function remove_vps($vps_id='',$force=null){

		if(!main::$is_staff){
			main::error_page("You cannot preform this action.");
		}

		if($vps_id == '' && isset(dev::$get['remove_vps'])){
			$vps_id = dev::$get['remove_vps'];
		}

		if($force == null && isset(dev::$post['force_delete'])){
			$force = true;
		}

		if($force == null){
			$force = false;
		}

		if($vps_id != ''){
			$vo_vps = func_vps::get_vps_by_id($vps_id);
			if(count($vo_vps) == 0){
				main::error_page("VM not found.");
			}
			$vo_vps = $vo_vps[0];

			//Check for Force Delete
			if($force === true){
				$force_delete = true;
			}
			else
			{
				$force_delete = false;
			}

			event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was queued for removal.');

			if($vo_vps->get_real_id() != ''){

				$background = true;

				main::$cnf['main']['success_codes'] .= ' | XEN_MOUNT_LOOP';

				$vpsDriver = new vps_operations($vo_vps);
				$vpsDriver->stop_vps($background);
				$vpsDriver->remove_all_ips($background,'stop');
				$vpsDriver->destroy_vps($force_delete,$background,'remove_all_ips');
				$vpsDriver->execute();

			} else {
				$force_delete = true;
			}

			if((isset($vpsDriver) && $vpsDriver->isOkay()) || $force_delete){

				if($force_delete){
					dao_vps::remove(
						" WHERE vps_id = :vps_id",
						array("vps_id"=>$vo_vps->get_vps_id())
					);

					dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['ip_map']." WHERE vps_id = '".$vo_vps->get_vps_id()."' ");
					dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['vps_user_map']." WHERE vps_id = '".$vo_vps->get_vps_id()."' ");
					dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['vps_stats']." WHERE vps_id = '".$vo_vps->get_vps_id()."' ");
					dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['vps_status_history']." WHERE vps_id = '".$vo_vps->get_vps_id()."' ");
				}

				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM has been queued for removal!"));
				return true;
			}
			else
			{
				main::set_action_message(self::get_vm_action_message($vpsDriver,"VM removal has failed!"));
				return false;
			}
		}

	}

	public static function browse_action_delete(){

		if(!main::$is_staff){
			main::error_page("You cannot preform this action.");
		}

		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				$output = '';
				foreach(dev::$post['browse_action'] AS $delete){

					if(isset(dev::$post['force_delete'])){
						$force = true;
					} else {
						$force = false;
					}

					self::remove_vps($delete,$force);

				}
				main::set_action_message($output.'VM\'s have been queued for removal!');
			}
		}
	}

}

?>
