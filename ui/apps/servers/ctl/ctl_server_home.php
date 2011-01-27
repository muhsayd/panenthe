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

class ctl_server_home {

	private $is_show_home = true;
	private $vo_server;

	public function __construct(){

		//Get Server
		$this->get_server();

		//Server Mods
		$this->ssh_port();
		$this->details();
		$this->root_password();
		$this->hostname();
		$this->setup_keys();
		$this->install_driver();
		$this->remove_driver();

		//Server Actions
		$this->reboot();
		$this->stop();
		$this->setup_network();

		//Server Home
		$this->show_home();

	}

	private function get_server(){

		$server = func_servers::get_server_by_id(dev::$get['server_id']);

		if(isset($server[0])){
			$this->vo_server = $server[0];
			dev::$tpl->set_constant("server_id",$this->vo_server->get_server_id());
		}
		else
		{
			echo "Server not found.";
			exit;
		}
		
	}
	
	private function hostname(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'hostname'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'change_hostname'){
				$this->is_show_home = func_server_home::change_hostname();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{
				$fields['hostname']		=	$this->vo_server->get_hostname();
				vw_server_home::hostname_form($fields);
			}

		}
	}
	
	private function ssh_port(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'ssh_port'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'change_ssh'){
				$this->is_show_home = func_server_home::change_ssh_port();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{
				$fields['hostname']		=	$this->vo_server->get_hostname();
				$fields['port']			=	$this->vo_server->get_port();
				vw_server_home::ssh_form($fields);
			}

		}
	}

	private function root_password(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'root_password'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'root_password'){
				$this->is_show_home = func_server_home::change_root_password();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{
				$fields['hostname']				=		$this->vo_server->get_hostname();
				vw_server_home::root_password_form($fields);
			}

		}
	}
	
	private function details(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'details'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'details'){
				$this->is_show_home = func_server_home::change_details();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{

				$fields['server_id']			=	$this->vo_server->get_server_id();
				$fields['parent_server_id']	=		$this->vo_server->get_parent_server_id();
				$fields['name']				=		$this->vo_server->get_name();
				$fields['datacenter']			=	$this->vo_server->get_datacenter();
				$fields['ip']					=	$this->vo_server->get_ip();
				$fields['hostname']			=	$this->vo_server->get_hostname();
				$fields['port']				=	$this->vo_server->get_port();
				$fields['password']			=	$this->vo_server->get_password();
				$fields['created']			=	$this->vo_server->get_created();
				$fields['modified']			=	$this->vo_server->get_modified();
				vw_server_home::details_form($fields);
			}

		}
	}
	
	private function setup_keys(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'setup_keys'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'setup_keys'){
				$this->is_show_home = func_server_home::setup_keys();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{
				$fields['hostname']		=	$this->vo_server->get_hostname();
				$fields['password']		=	'';
				vw_server_home::setup_keys_form($fields);
			}

		}
	}
	
	private function install_driver(){
	
		if(isset(dev::$get['act']) && dev::$get['act'] == 'install_driver'){
		
			if(isset(dev::$post['action']) && dev::$post['action'] == 'install_driver'){
				$this->is_show_home = func_server_home::install_driver();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{
				if(isset(dev::$post['driver_id'])){
					$driver_id = dev::$post['driver_id'];
				} else {
					$driver_id = false;
				}
				
				$fields['hostname']		=	$this->vo_server->get_hostname();
				$fields['drivers']		=	func_server_home::get_drivers('driver_id',$driver_id);
				
				vw_server_home::install_driver_form($fields);
				
			}

		}
		
	}
	
	private function remove_driver(){
		
		if(isset(dev::$get['act']) && dev::$get['act'] == 'remove_driver'){
		
			if(isset(dev::$post['action']) && dev::$post['action'] == 'remove_driver'){
				$this->is_show_home = func_server_home::remove_driver();
			}
			else
			{
				$this->is_show_home = false;
			}
			
			if(!$this->is_show_home)
			{
				if(isset(dev::$post['driver_id'])){
					$driver_id = dev::$post['driver_id'];
				} else {
					$driver_id = false;
				}
				
				$fields['hostname']		=	$this->vo_server->get_hostname();
				$fields['drivers']		=	func_server_home::get_drivers('driver_id',$driver_id);
				
				vw_server_home::remove_driver_form($fields);
				
			}

		}
		
	}

	private function start(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'start'){
			$vpsDriver = new vps_operations($this->vo_vps);
			$vpsDriver->start_vps();
			$vpsDriver->execute();
			if($vpsDriver->isOkay()){
				main::set_action_message("VPS has been started!");
			}
			else
			{
				main::set_action_message($vpsDriver->getOutput()."<br />VPS has failed to start!");
			}
		}
	}

	private function reboot(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'reboot'){
			$serverDriver = new server_operations($this->vo_server);
			$serverDriver->reboot_server();
			$serverDriver->execute();
			if($serverDriver->isOkay()){
				main::set_action_message("Server has been rebooted!");
			}
			else
			{
				main::set_action_message($serverDriver->getOutput()."<br />Server has failed to reboot!");
			}
		}
	}
	
	private function setup_network(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'setup_network'){
			func_server_home::setup_network();
			$this->is_show_home = true;
		}
	}

	private function stop(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'stop'){
			$serverDriver = new server_operations($this->vo_server);
			$serverDriver->stop_server();
			$serverDriver->execute();
			if($serverDriver->isOkay()){
				main::set_action_message("Server has been shutdown!");
			}
			else
			{
				main::set_action_message("Server has failed to shutdown!");
			}
		}
	}

	private function show_home(){

		if($this->is_show_home){
			
			$serverDriver = new server_operations($this->vo_server);
			
			//Update Stats
			$serverDriver->stats_server();
			$serverDriver->execute();
			
			//Get Stats
			list($stats,$limits) = func_servers::get_stats($this->vo_server);
			vw_server_home::load_js();
			vw_server_home::server_home($this->vo_server,$stats,$limits);
		}
		
	}

}

?>
