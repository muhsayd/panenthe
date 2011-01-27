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

class server_operations {

	public $debug;
	
	private $data;
	private $m_vo;
	private $vo;
	private $okay;
	private $output;
	private $codes;
	private $commands;
	private $ctl;
	
	public function __construct($vo_server,$master=false){
		$this->set_debug();
		$this->get_master_server();
		$this->okay = true;
		$this->vo = $vo_server;
		$this->set_ctl($master);
		$this->server_driver_data($master);
	}
	
	private function get_master_server(){
		
		$rows = dao_servers::select('WHERE parent_server_id = 0',array());
		if(!isset($rows[0])){
			main::error_page("There is no master server!");
		}
		$this->m_vo = $rows[0];
		
	}
	
	private function set_debug(){
		if(main::$cnf['ui_config']['server_debug'] == 'true'){
			$this->debug = true;
		}
		else
		{
			$this->debug = false;
		}
	}
	
	private function set_ctl($master){
		if($this->vo->get_parent_server_id() == 0 || $master === true){
			$this->ctl = 'masterctl';
		}
		else
		{
			$this->ctl = 'serverctl';
		}
	}
	
	public function get_codes(){
		return $this->codes;
	}
	
	private function add_output($output_array,$command='Command'){
		
		if(isset($output_array[0])){
			$err_code = (string) trim($output_array[0]);
			$this->codes[] = $err_code;
			if(isset(main::$err[$err_code])){
				$error_message = main::$err[$err_code];
				if($this->debug){
					dev::output_r(main::$err[$err_code]);
				}
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
			dev::output_r($output_array,$error_message);
		}

		if($error_message !== false && $error_message['code'] != '0000'){
			$this->okay = false;
			$this->output .=
				'<strong>Command:</strong> '.$command.' '.
				'<strong>Code:</strong> '.$error_message['code'].' '.
				'<strong>Severity:</strong> '.$error_message['severity'].' '.
				'<strong>Message:</strong> '.$error_message['message'].' '.
				'<strong>More Information:</strong> <a href="'.$error_message['url'].'">'.$error_message['url'].'</a>'.' '.
				' <br /> ';
		}
		elseif($error_message === false){
			$this->okay = false;
			$this->output .= 'An uncaught error has occurred.</br />';
		}else{
			//This Means Success
		}
	}
	
	private function add_call($cmd,$act,$data,$desc,$event){
		
		$this->commands[]= array(
			"cmd"	=>	$cmd,
			"act"	=>	$act,
			"data"	=>	$data,
			"desc"	=>	$desc,
			"event"	=>	$event
		);
		
	}
	
	private function server_driver_data($master=true){
		
		require_once(main::$cnf['main']['root_dir'].'/shared/bridge/python.php');

		if($master){
			$server_data = array(
				"server_id"			=>	$this->m_vo->get_server_id(),
				"parent_server_id"	=>	$this->m_vo->get_parent_server_id(),
				"hostname"			=>	$this->m_vo->get_hostname(),
				"ip"				=>	$this->m_vo->get_ip(),
				"port"				=>	$this->m_vo->get_port()
			);
		 }
		 else
		 {
		 	 $server_data = array(
				"server_id"			=>	$this->m_vo->get_server_id(),
				"parent_server_id"	=>	$this->m_vo->get_parent_server_id(),
				"hostname"			=>	$this->m_vo->get_hostname(),
				"ip"				=>	$this->m_vo->get_ip(),
				"port"				=>	$this->m_vo->get_port()
			);
			$server_data['remote_server'] = array(
				"server_id"			=>	$this->vo->get_server_id(),
				"parent_server_id"	=>	$this->vo->get_parent_server_id(),
				"hostname"			=>	$this->vo->get_hostname(),
				"ip"				=>	$this->vo->get_ip(),
				"port"				=>	$this->vo->get_port()
			);
		}

		$this->data = $server_data;
		
	}
	
	private function driver_data($driver){
		
		if(is_object($driver)){
			$driver = $driver->get_ext_ref();
		}
		
		$data = $this->data;
		$server_data['server'] = $data;
		$server_data['driver'] = $driver;
		$server_data['reboot'] = 'true';
	
		return $server_data;
		
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
				if($this->debug){
					dev::output_r($command['data']);
				}
				$output = backend_call($command['cmd'],$command['act'],$command['data'], $this->debug);
				event_api::add_event($command['event']);
				$this->add_output($output,$command['desc']);
			}
		}
		
		if($clearCommands){
			$this->commands = array();
		}
		
	}
	
	public function setup_keys(){

		$server_data = $this->data;
		$vo_server = $this->vo;

		$server_data['remote_server'] = array(
			"server_id"			=>	$this->vo->get_server_id(),
			"parent_server_id"	=>	$this->vo->get_parent_server_id(),
			"hostname"			=>	$this->vo->get_hostname(),
			"ip"				=>	$this->vo->get_ip(),
			"port"				=>	$this->vo->get_port(),
			"password"			=>	$this->vo->get_password()
		);

		//Make Create Call
		$this->add_call(
			$this->ctl, 
			'install_key', 
			$server_data,
			"Add Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" keys were reinitialized.'
		);

	}
	
	public function setup_network(){

		$server_data = $this->data;
		$vo_server = $this->vo;

		$server_data['remote_server'] = array(
				"server_id"			=>	$this->vo->get_server_id(),
				"parent_server_id"	=>	$this->vo->get_parent_server_id(),
				"hostname"			=>	$this->vo->get_hostname(),
				"ip"				=>	$this->vo->get_ip(),
				"port"				=>	$this->vo->get_port(),
				"password"			=>	$this->vo->get_password()
		);

		//Make Setup Call
		$this->add_call(
			'serverctl',
			'setup',
			$server_data,
			"Setup Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was setup.'
		);

	}

	public function add_server(){

		$server_data = $this->data;
		$vo_server = $this->vo;

		$server_data['remote_server'] = array(
				"server_id"			=>	$this->vo->get_server_id(),
				"parent_server_id"	=>	$this->vo->get_parent_server_id(),
				"hostname"			=>	$this->vo->get_hostname(),
				"ip"				=>	$this->vo->get_ip(),
				"port"				=>	$this->vo->get_port(),
				"password"			=>	$this->vo->get_password()
		);

		//Make Create Call
		$this->add_call(
			$this->ctl, 
			'install_key', 
			$server_data,
			"Add Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was added.'
		);

		//Make Setup Call
		$this->add_call(
			'serverctl',
			'setup',
			$server_data,
			"Setup Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was setup.'
		);

	}

	public function remove_server(){
	
		$server_data = $this->data;
		$vo_server = $this->vo;
	
		$server_data['remote_server'] = array(
				"server_id"			=>	$this->vo->get_server_id(),
				"parent_server_id"	=>	$this->vo->get_parent_server_id(),
				"hostname"			=>	$this->vo->get_hostname(),
				"ip"				=>	$this->vo->get_ip(),
				"port"				=>	$this->vo->get_port(),
				"password"			=>	$this->vo->get_password()
		);
	
		//Make Setup Call
		$this->add_call(
			'serverctl',
			'usetup',
			$server_data,
			"Clenaup Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was cleaned.'
		);
	
		$this->add_call(
			$this->ctl,
			'remove_key',
			$server_data,
			"Remove Server",
			"Server #".$vo_server->get_server_id()." \"".$vo_server->get_hostname()."\" was removed."
		);

	}

	public function set_passwd($password,$username="root"){

		$server_data = $this->data;
		$vo_server = $this->vo;
	
		$server_data['username'] = $username;
		$server_data['password'] = $password;
	
		$this->add_call(
			$this->ctl,
			'passwd',
			$server_data,
			"Change Password",
			"Server #".$vo_server->get_server_id()." \"".$vo_server->get_hostname()."\" user \"".
			$username."\" had his/her password changed."
		);
	
	}

	public function set_ssh_port($new_ssh_port){

		$server_data = $this->data;
		$vo_server = $this->vo;
	
		$server_data['new_ssh_port'] = $new_ssh_port;
	
		$this->add_call(
			$this->ctl,
			'ssh_port',
			$server_data,
			"Change SSH Port",
			"Server #".$vo_server->get_server_id()." \"".$vo_server->get_hostname()." SSH port was changed to ".$new_ssh_port."."
		);
	
	}

	 public function set_hostname($new_hostname){

		$server_data = $this->data;
		$vo_server = $this->vo;
	
		$server_data['new_hostname'] = $new_hostname;
	
		$this->add_call(
			$this->ctl,
			'set_hostname',
			$server_data,
			"Change Hostname",
			"Server #".$vo_server->get_server_id()." \"".$vo_server->get_hostname()."\" Hostname was changed to ".$new_hostname."."
		);
	
	}

	public function stop_server(){

		$server_data = $this->data;
		$vo_server = $this->vo;

		//Make the Stop Call
		$this->add_call(
			$this->ctl, 
			'shutdown', 
			$server_data,
			"Stop Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was stopped.'
		);

	}

	public function reboot_server(){

		$server_data = $this->data;
		$vo_server = $this->vo;

		//Make the Reboot Call
		$this->add_call(
			$this->ctl, 
			'reboot', 
			$server_data,
			"Reboot Server",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was rebooted.'
		);$server_data['driver'] = $driver;

	}

	public function status_server(){

		return;
		$vps_data = $this->data;
		$vo_vps = $this->vo;

		//Make the Status Call
		$status_output = backend_call('vpsctl', 'status', $vps_data,$this->debug);
	
		if($this->debug){
			dev::output_r($status_output);
		}

		if(isset($status_output[0]) && isset(main::$err[$status_output[0]])){
			return main::$err[$status_output[0]];
		}
		else
		{
			return false;
		}

	}

	public function stats_server(){

		$server_data = $this->data;
		$vo_server = $this->vo;
		
		//Make the stat Call
		$this->add_call(
			$this->ctl, 
			'status_update_all', 
			$server_data,
			"Server Stats",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" was polled for usage.'
		);

	}

	public function install_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Install Call
		$this->add_call(
			'driverctl',
			'install',
			$server_data,
			"Install Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was installed.'
		);
		
	}
	
	public function uninstall_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the uninstall call
		$this->add_call(
			'driverctl',
			'uninstall',
			$server_data,
			"Uninstall Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was uninstalled.'
		);
		
	}
	
	public function activate_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'activate',
			$server_data,
			"Activate Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was activated.'
		);
		
	}
	
	public function deactivate_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'deactivate',
			$server_data,
			"Activate Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was deactivated.'
		);
		
	}
	
	public function start_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'start',
			$server_data,
			"Start Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was started.'
		);
		
	}
	
	public function stop_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'stop',
			$server_data,
			"Stop Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was stopped.'
		);
		
	}
	
	public function restart_driver($driver){
	
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'restart',
			$server_data,
			"Restart Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was restarted.'
		);
		
	}
	
	public function driver_status($driver){
	
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'status',
			$server_data,
			"Status Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was polled for status.'
		);
		
	}
	
	public function cleanup_driver($driver){
		
		$vo_server = $this->vo;
		$server_data = $this->driver_data($driver);
		
		//Make the Activate Call
		$this->add_call(
			'driverctl',
			'cleanup',
			$server_data,
			"Cleanup Driver",
			'Server #'.$vo_server->get_server_id().' "'.$vo_server->get_hostname().'" a driver was cleaned up.'
		);
		
	}
	
}

?>
