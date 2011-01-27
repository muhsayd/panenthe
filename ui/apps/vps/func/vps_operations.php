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

require_once(main::$cnf['main']['root_dir'].'/shared/bridge/python.php');

class vps_operations {

	const OVZ = 'ovz';
	const XEN = 'xen';

	public $debug;
	
	private $data;
	private $vo;
	private $okay;
	private $output;
	private $commands;
	
	static $queueIds;
	
	public function __construct($vo_vps){
		$this->set_debug();
		$this->okay = true;
		$this->vo = $vo_vps;
		$this->vps_driver_data();
	}
	
	private function set_debug(){
		if(main::$cnf['ui_config']['vps_debug'] == 'true'){
			$this->debug = true;
		}
		else
		{
			$this->debug = false;
		}
	}
	
	public function set_parent($value){
		$this->parent = $value;
	}
	
	public function set_background($value){
		$this->background = $value;
	}
	
	private function add_output($output_array,$command='Command'){
		
		if(isset($output_array[0])){
			$err_code = (string) trim($output_array[0]);
			if(isset(main::$err[$err_code])){
				$error_message = main::$err[$err_code];
			}
			else
			{
				$error_message = false;
			}
		}
		else
		{
			$error_message = false;
		}
		
		if($this->debug){
			dev::output_r($output_array,$error_message,time());
		}
		
		//Multiple Sucess Codes
		$success = explode('|',main::$cnf['main']['success_codes']);
		foreach($success AS &$const){
			$const = trim($const);
		}
		$success_codes = array();
		foreach(main::$err AS $code => $code_data){
			if(in_array($code_data['constant'],$success)){
				$success_codes[] = $code;
			}
		}
		if($error_message !== false && !in_array($error_message['code'],$success_codes)){
			$this->okay = false;
			$this->output .=
				'<strong>Command:</strong> '.$command.' <br />'.
				'<strong>Code:</strong> '.$error_message['code'].' <br />'.
				'<strong>Severity:</strong> '.$error_message['severity'].' <br />'.
				'<strong>Message:</strong> '.$error_message['message'].' <br />'.
				'<strong>More Information:</strong> <a href="'.$error_message['url'].'">'.$error_message['url'].'</a>'.' <br />'.
				' <br /> ';
		}
		elseif($error_message === false){
			$this->okay = false;
			$message = implode(' ',$output_array);
			$this->output .= 'An uncaught error has occurred: '.$message.'</br />';
		}else{
			//This Means Success
		}
		
	}
	
	private function add_call($cmd,$act,$data,$desc,$event,$background=false,$parent=''){
		
		$this->commands[]= array(
			"cmd"			=>	$cmd,
			"act"			=>	$act,
			"data"			=>	$data,
			"desc"			=>	$desc,
			"event"			=>	$event,
			"background"	=>	$background,
			"parent"		=>	$parent
		);
		
	}
	
	private function vps_driver_data(){
		
		$vo_vps =& $this->vo;

		$defaults = array(
			"disk_space"		=>		"2000",
			"backup_space"		=>		"0",
			"swap_space"		=>		"256",
			"g_mem"				=>		"256",
			"b_mem"				=>		"256",
			"cpu_pct"			=>		"100",
			"cpu_num"			=>		"1",
			"in_bw"				=>		"10",
			"out_bw"			=>		"10"
		);

		//Get Server
		$server = dao_servers::select(
			' WHERE server_id = :v_server_id ',
			array(
				"v_server_id"   =>    $vo_vps->get_server_id()
			)
		);

		if(isset($server[0])){
			$server = $server[0];
		}
		else
		{
			$server = false;
		}
	
		//Get Master
		$master = dao_servers::select(
			' WHERE server_id = :v_server_id ',
			array(
				"v_server_id"   =>    $server->get_parent_server_id()
			)
		);

		if(isset($master[0])){
			$master = $master[0];
		}
		else
		{
			$master = false;
		}

		//Get Driver
		$driver = dao_drivers::select(
			' WHERE driver_id = :v_driver_id ',
			array(
				"v_driver_id"	=>	$vo_vps->get_driver_id()
			)
		);

		if(isset($driver[0])){
			$driver = $driver[0];
		}
		else
		{
			$driver = false;
		}

		//Get OST
		$ost = dao_ost::select(
			' WHERE ost_id = :v_ost_id ',
			array(
				"v_ost_id"		=>	$vo_vps->get_ost()
			)
		);

		if(isset($ost[0])){
			$ost = $ost[0];
		}
		else
		{
			$ost = false;
		}

		//Get OST Image
		if($ost){
			$ost_image = str_replace(dirname($ost->get_path()).'/','',$ost->get_path());
		}
		
		//Check OS Type
		switch($ost->get_os_type()){
			
			case 'LINUX':
				$os_type = 'LINUX';
			break;
			
			case 'WINDOWS':
				$os_type = 'WINDOWS';
			break;
			
			case 'OTHER':
			default:
				$os_type = 'OTHER';
			break;
			
		}

		if($driver && $server && $ost){
			$vps_data = array(
				"vps_id"		=>		$vo_vps->get_vps_id(),
				"real_id"		=>		$vo_vps->get_real_id(),
				"driver"		=>		$driver->get_ext_ref(),
				"hostname"	=>		$vo_vps->get_hostname(),
				"server"		=>		array(
					"server_id"			=>	$server->get_server_id(),
					"parent_server_id"	=>	$server->get_parent_server_id(),
					"hostname"			=>	$server->get_hostname(),
					"ip"				=>	$server->get_ip(),
					"port"				=>	$server->get_port()
				),
				"master"	=>	array(
					"server_id"			=>	$master->get_server_id(),
					"parent_server_id"	=>	$master->get_parent_server_id(),
					"hostname"			=>	$master->get_hostname(),
					"ip"				=>	$master->get_ip(),
					"port"				=>	$master->get_port()
				),
				"disk_space"			=>	(int) $vo_vps->get_disk_space(),
				"backup_space"			=>	(int) $vo_vps->get_backup_space(),
				"swap_space"			=>	(int) $vo_vps->get_swap_space(),
				"g_mem"					=>	(int) $vo_vps->get_g_mem(),
				"b_mem"					=>	(int) $vo_vps->get_b_mem(),
				"cpu_pct"				=>	(int) $vo_vps->get_cpu_pct(),
				"cpu_num"				=>	(int) $vo_vps->get_cpu_num(),
				"in_bw"					=>	(int) $vo_vps->get_in_bw(),
				"out_bw"				=>	(int) $vo_vps->get_out_bw(),
				"ost"					=>	$ost_image,
				"os_type"				=>	$os_type,
				"kernel"				=>	$vo_vps->get_kernel()
			);
		}
		else
		{
			$vps_data = array();
		}

		foreach($defaults AS $key => $value){
			if($vps_data[$key] == ""){
				$vps_data[$key] = $value;
			}
		}

		$this->data = $vps_data;

	}
	
	public function getOutput(){
		return $this->output;
	}
	
	public function isOkay(){
		return $this->okay;
	}
	
	public function execute($clearCommands=true){
		if(is_array($this->commands)){
			foreach($this->commands AS $command){
			
				$background = $command['background'];
				
				if(!is_array($command['parent'])){
					if(isset(self::$queueIds[$command['parent']])){
						$queueId = array(self::$queueIds[$command['parent']]);
					} else {
						$queueId = array(0);
					}
				} else {
					$queueId = array();
					foreach($command['parent'] AS $cmd){
						if(isset(self::$queueIds[$cmd])){
							$queueId[] = self::$queueIds[$cmd];
						}
					}
				}
				
				//Disable queuing
				if(
					isset(main::$cnf['ui_config']['disable_queue']) &&
					main::$cnf['ui_config']['disable_queue'] == 'true'
				){
					$background = false;
					$queueId = false;
				}
				
				$output = backend_call(
					$command['cmd'],
					$command['act'],
					$command['data'],
					$this->debug,
					$background,
					$queueId
				);
				
				if($background && isset($output[1])){
					self::$queueIds[$command['act']] = $output[1];
				}
				
				if(!$background){
					event_api::add_event($command['event']);
				}
				$this->add_output($output,$command['desc']);
			}
		}
		
		if($clearCommands){
			$this->commands = array();
		}
		
	}
	
	public function next_id(){
		
		$vps_data = $this->data;
		unset($vps_data['real_id']);
		
		$output = backend_call('vpsctl','next_id',$vps_data,$this->debug);
		dev::output_r($output);
		
		return $output;
		
	}

	public function create_vps(){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make Create Call
		$this->add_call(
			'vpsctl', 
			'create', 
			$vps_data,
			"Create VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" queue for creation.',
			true
		);

	}
	
	public function lock(){
	
		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make Create Call
		$this->add_call(
			'vpsctl', 
			'lock', 
			$vps_data,
			"Lock VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was locked for operations.',
			true
		);
		
	}
	
	public function unlock($parent=''){
	
		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make Create Call
		$this->add_call(
			'vpsctl', 
			'unlock', 
			$vps_data,
			"Unlock VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was unlocked after operations.',
			true,
			$parent
		);
		
	}
	
	public function set_dns($dns,$background=false,$parent=''){
	
		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		if($vps_data['driver'] == self::XEN){
			$this->poweroff_vps($background,$parent);
		}
	
		$vps_data['dns'] = trim($dns);
	
		$this->add_call(
			'vpsctl',
			'set_dns',
			$vps_data,
			"Set DNS",
			"Virtual Machine #".$vo_vps->get_vps_id()." \"".$vo_vps->get_hostname()."\" DNS servers were added.",
			$background,
			$parent
		);
		
		if($vps_data['driver'] == self::XEN){
			$this->start_vps($background,$parent);
		}

	}
	
	public function set_hostname($hostname,$background=false,$parent=''){
	
		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		$vps_data['new_hostname'] = $hostname;
		
		$this->add_call(
			'vpsctl',
			'set_hostname',
			$vps_data,
			"Set Hostname",
			"Virtual Machine #".$vo_vps->get_vps_id()." \"".$vo_vps->get_hostname()."\" hostname was set to ".$hostname.".",
			$background,
			$parent
		);
		
	}
	
	public function add_ips($ip_map=array(),$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		if($vps_data['driver'] == self::XEN){
			$this->poweroff_vps($background,$parent);
		}

		if(is_array($ip_map)){
			foreach($ip_map AS $ip){
				if(!is_null($ip)){
					$vps_data['ip'] = $ip['ip'];
					$vps_data['netmask'] = $ip['netmask'];
					$vps_data['gateway'] = $ip['gateway'];
					$this->add_call(
						'vpsctl',
						'add_ip',
						$vps_data,
						"Add IP",
						'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" ip '.$ip['ip'].' was added.',
						$background,
						$parent
					);
				}
			}
		}
		
		if($vps_data['driver'] == self::XEN){
			$this->start_vps($background,$parent);
		}

	}

	public function remove_ips($ip_map=array(),$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		if($vps_data['driver'] == self::XEN){
			$this->poweroff_vps($background,$parent);
		}
		
		if(is_array($ip_map)){
			foreach($ip_map AS $ip){
				if(!is_null($ip)){
					$vps_data['ip'] = $ip['ip'];
					$this->add_call(
						'vpsctl',
						'remove_ip',
						$vps_data,
						"Remove IP",
						'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" ip '.$ip['ip'].' was removed.',
						$background,
						$parent
					);
				}
			}
		}
		
		if($vps_data['driver'] == self::XEN){
			$this->start_vps($background,$parent);
		}
		
	}

	public function suspend($ip_map=array(),$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		if(is_array($ip_map)){
			foreach($ip_map AS $ip){
				$vps_data['ip'] = $ip['ip'];
				$this->add_call(
					'vpsctl',
					'suspend_ip',
					$vps_data,
					"Suspend IP",
					'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" ip '.$ip['ip'].' was suspended.',
					$background,
					$parent
				);
			}
		}

	}

	public function unsuspend($ip_map=array(),$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		if(is_array($ip_map)){
			foreach($ip_map AS $ip){
				$vps_data['ip'] = $ip['ip'];
				$this->add_call(
					'vpsctl',
					'unsuspend_ip',
					$vps_data,
					"Unsuspend IP",
					'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" ip '.$ip['ip'].' was unsuspended.',
					$background,
					$parent
				);
			}
		}

	}

	public function set_passwd($user,$pass,$background=false,$parent=''){

		$vo_vps = $this->vo;
		$vps_data = $this->data;
		$vps_data['username'] = $user;
		$vps_data['password'] = $pass;
		
		if($vps_data['driver'] == self::XEN){
			$this->poweroff_vps($background,$parent);
		}

		$this->add_call(
			'vpsctl',
			'passwd',
			$vps_data,
			"Change Password",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" password for '.$user.' was changed.',
			$background,
			$parent
		);
		
		if($vps_data['driver'] == self::XEN){
			$this->start_vps($background,$parent);
		}
		
	}

	public function mod_vps($old_vo,$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		if($vps_data['driver'] == self::XEN){
			$this->poweroff_vps($background,$parent);
		}
		
		//Add Old Data
		$vps_data['old_data'] = array(
			"disk_space"	=>	(int) $old_vo->get_disk_space(),
			"backup_space"	=>	(int) $old_vo->get_backup_space(),
			"swap_space"	=>	(int) $old_vo->get_swap_space(),
			"kernel"		=>	$old_vo->get_kernel(),
			"g_mem"			=>	(int) $old_vo->get_g_mem(),
			"b_mem"			=>	(int) $old_vo->get_b_mem(),
			"cpu_pct"		=>	(int) $old_vo->get_cpu_pct(),
			"cpu_num"		=>	(int) $old_vo->get_cpu_num(),
			"in_bw"			=>	(int) $old_vo->get_in_bw(),
			"out_bw"		=>	(int) $old_vo->get_out_bw()
		);

		//Make the Modify Call
		$mod_output = $this->add_call(
			'vpsctl', 
			'modify', 
			$vps_data,
			"Modify VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was modified.',
			$background,
			$parent
		);
		
		if($vps_data['driver'] == self::XEN){
			$this->start_vps($background,$parent);
		}
		
	}

	public function rebuild_vps($background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		//This is kinda hacky because destroy needs force -- its whatever
		$vps_data['force'] = true;

		//Make the Rebuild Call
		$this->add_call(
			'vpsctl', 
			'rebuild', 
			$vps_data,
			"Rebuild VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was rebuilt.',
			$background,
			$parent
		);

	}

	public function create_ost($ost_name,$ost_driver_id,$ost_arch,$ost_file,$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		$vps_data['ost_name'] = $ost_name;
		$vps_data['ost_driver_id'] = $ost_driver_id;
		$vps_data['ost_arch'] = $ost_arch;
		$vps_data['ost_file'] = $ost_file;
		
		//Make the Rebuild Call
		$this->add_call(
			'vpsctl', 
			'ost_create', 
			$vps_data,
			"Create OST",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" had a OS Template created from it.',
			$background,
			$parent
		);

	}

	public function start_vps($background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Start Call
		$this->add_call(
			'vpsctl', 
			'start', 
			$vps_data,
			"Start VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was started.',
			$background,
			$parent
		);

	}

	public function stop_vps($background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Stop Call
		$this->add_call(
			'vpsctl', 
			'stop', 
			$vps_data,
			"Stop VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was stopped.',
			$background,
			$parent
		);

	}

	public function reboot_vps($background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Reboot Call
		$this->add_call(
			'vpsctl', 
			'reboot', 
			$vps_data,
			"Reboot VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was rebooted.',
			$background,
			$parent
		);

	}

	public function poweroff_vps($background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Poweroff Call
		$this->add_call(
			'vpsctl', 
			'poweroff', 
			$vps_data,
			"Poweroff VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was powered off.',
			$background,
			$parent
		);

	}

	public function destroy_vps($force=false,$background=false,$parent=''){

		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		if($force === true){
			$vps_data['force'] = true;
		} else {
			$vps_data['force'] = false;
		}

		$this->add_call(
			'vpsctl', 
			'destroy', 
			$vps_data,
			"Destory VM",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was destroyed.',
			$background,
			$parent
		);

	}
	
	public function remove_all_ips($background=false,$parent=''){
		
		$vps_data = $this->data;
		$vo_vps = $this->vo;
		
		if($vps_data['driver'] == self::XEN){
			$this->poweroff_vps($background,$parent);
		}
		
		$this->add_call(
			'vpsctl', 
			'remove_all_ips', 
			$vps_data,
			"Remove All Ips",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" had all ips removed.',
			$background,
			$parent
		);
		
		if($vps_data['driver'] == self::XEN){
			$this->start_vps($background,$parent);
		}

	}

	public function status_vps(){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Status Call
		$status_output = backend_call('vpsctl', 'status', $vps_data,$this->debug);
		
		if($this->debug){
			dev::output_r($status_output);
		}

		if(isset($status_output[0]) && isset(main::$err[$status_output[0]])){
			$status = main::$err[$status_output[0]];
			//Enter Into Status History
			func_vps::insert_status_history($this->vo,$status);
			return $status;
		}
		else
		{
			return false;
		}

	}

	public function stats_vps(){

		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Status Call
		$status_output = backend_call('vpsctl', 'status_update_all', $vps_data,$this->debug);
		
		if($this->debug){
			dev::output_r($status_output);
		}

		if(isset($status_output[0]) && isset(main::$err[$status_output[0]])){
			$status = main::$err[$status_output[0]];
			//Enter Into Status History
			func_vps::insert_status_history($this->vo,$status);
			return $status;
		}
		else
		{
			return false;
		}
		
		/*
		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Stop Call
		$this->add_call(
			'vpsctl', 
			'status_update_all', 
			$vps_data,
			"VM Stats",
			'Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" was polled for usage.'
		);
		*/
		
	}


}

?>
