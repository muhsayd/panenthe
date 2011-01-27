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

class func_server_home {

	public static function get_srv_action_message(&$serverDriver,$success_message){
		return $serverDriver->getOutput() .' '.$success_message;
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

	public static function change_root_password(){

		if(isset(dev::$post['server_id'])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$rows = dao_servers::select(
				"WHERE server_id = :v_server_id",
				array("v_server_id"=>dev::$post['server_id'])
			);
			if(isset($rows[0])){
				$vo_server = $rows[0];
			}
			else
			{
				echo "Server not found.";
				exit;
			}

			if(dev::$post['root_password'] == ''){
				main::set_action_message("Root password cannot be left blank.");
				return false;
			}
			
			if(dev::$post['root_password'] != dev::$post['confirm_root_password']){
				main::set_action_message("Root passwords must match");
				return false;
			}
			
			//Get Server Driver
			$serverDriver = new server_operations($vo_server);
			$serverDriver->set_passwd(dev::$post['root_password'],'root');
			$serverDriver->execute();
			
			if($serverDriver->isOkay()){
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server root password has been changed!"
					)
				);
				return true;
			}
			else
			{
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server root password change has failed!"
					)
				);
				return false;
			}
		}
	}
	
	public static function change_ssh_port(){

		if(isset(dev::$post['server_id'])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$rows = dao_servers::select(
				"WHERE server_id = :v_server_id",
				array("v_server_id"=>dev::$post['server_id'])
			);
			if(isset($rows[0])){
				$vo_server = $rows[0];
			}
			else
			{
				echo "Server not found.";
				exit;
			}

			if(dev::$post['port'] == ''){
				main::set_action_message("SSH Port cannot be left blank.");
				return false;
			}
			
			//Get Server Driver
			$serverDriver = new server_operations($vo_server);
			$serverDriver->set_ssh_port(dev::$post['port']);
			$serverDriver->execute();
			
			if($serverDriver->isOkay()){
				$vo_server->set_port(dev::$post['port']);
				dao_servers::update(
					$vo_server->update_array(),
					" WHERE server_id = :v_server_id ",
					array("v_server_id"=>$vo_server->get_server_id())
				);
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server ssh port has been changed!"
					)
				);
				return true;
			}
			else
			{
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server ssh port change has failed!"
					)
				);
				return false;
			}
		}
	}
	
	public static function change_hostname(){

		if(isset(dev::$post['server_id'])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$rows = dao_servers::select(
				"WHERE server_id = :v_server_id",
				array("v_server_id"=>dev::$post['server_id'])
			);
			if(isset($rows[0])){
				$vo_server = $rows[0];
			}
			else
			{
				echo "Server not found.";
				exit;
			}

			if(dev::$post['hostname'] == ''){
				main::set_action_message("Hostname cannot be left blank.");
				return false;
			}
			
			//Get Server Driver
			$serverDriver = new server_operations($vo_server);
			$serverDriver->set_hostname(dev::$post['hostname']);
			$serverDriver->execute();
			
			if($serverDriver->isOkay()){
				$vo_server->set_hostname(dev::$post['hostname']);
				dao_servers::update(
					$vo_server->update_array(),
					" WHERE server_id = :v_server_id ",
					array("v_server_id"=>$vo_server->get_server_id())
				);
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server hostname has been changed!"
					)
				);
				return true;
			}
			else
			{
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server hostname has not been changed!"
					)
				);
				return false;
			}
		}
	}
	
	public static function setup_keys(){

		if(isset(dev::$post['server_id'])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$rows = dao_servers::select(
				"WHERE server_id = :v_server_id",
				array("v_server_id"=>dev::$post['server_id'])
			);
			if(isset($rows[0])){
				$vo_server = $rows[0];
			}
			else
			{
				main::error_page("Server not found.");
				exit;
			}

			if(dev::$post['password'] == ''){
				main::set_action_message("Password cannot be left blank.");
				return false;
			}
			
			//Get Server Driver
			$vo_server->set_password(dev::$post['password']);
			$serverDriver = new server_operations($vo_server,true);
			$serverDriver->setup_keys();
			$serverDriver->execute();
			
			if($serverDriver->isOkay()){
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server keys have been reintialized!"
					)
				);
				return true;
			}
			else
			{
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server keys could not be reinitialized!"
					)
				);
				return false;
			}
		}
	}
	
	public static function install_driver(){
		
		if(!isset(dev::$post['driver_id'])){
			main::set_action_message("Driver type was not provided.");
			return false;
		}
		
		if(!isset(dev::$post['server_id'])){
			main::set_action_message("Server could not be found.");
			return false;
		}
		
		//Get Server
		$server = func_servers::get_server_by_id(dev::$post['server_id']);
		$server = $server[0];
		
		//Get Driver To Install
		$driver = func_drivers::get_driver_by_id(dev::$post['driver_id']);
		$driver = $driver[0];
		
		//Start Up Operations
		$op = new server_operations($server);
		
		$drivers = dao_drivers::select(false);
		foreach($drivers AS $de_driver){
			//$op->deactivate_driver($de_driver->get_ext_ref());
		}
		
		//Install Driver and Activate
		$op->install_driver($driver->get_ext_ref());
		$op->activate_driver($driver->get_ext_ref());
		//$op->start_driver($driver->get_ext_ref());
		
		//Execute
		$op->execute();
		
		if($op->isOkay()){
			main::set_action_message(
				self::get_srv_action_message(
					$op,
					"Driver has been installed successfully!"
				)
			);
			return true;
		}
		else
		{
			main::set_action_message(
				self::get_srv_action_message(
					$op,
					"Driver could not be installed!"
				)
			);
			return false;
		}

	}
	
	public static function remove_driver(){
		
		if(!isset(dev::$post['driver_id'])){
			main::set_action_message("Driver type was not provided.");
			return false;
		}
		
		if(!isset(dev::$post['server_id'])){
			main::set_action_message("Server could not be found.");
			return false;
		}
		
		//Get Server
		$server = func_servers::get_server_by_id(dev::$post['server_id']);
		$server = $server[0];
		
		//Get Driver To Install
		$driver = func_drivers::get_driver_by_id(dev::$post['driver_id']);
		$driver = $driver[0];
		
		//Start Up Operations
		$op = new server_operations($server);
		
		//Install Driver and Activate
		$op->stop_driver($driver->get_ext_ref());
		//$op->deactivate_driver($driver->get_ext_ref()); --NOT IMPLEMENTED
		$op->uninstall_driver($driver->get_ext_ref());
		//$op->cleanup_driver($driver->get_ext_ref()); --NOT IMPLEMENTED
		
		//Execute
		$op->execute();
		
		if($op->isOkay()){
			main::set_action_message(
				self::get_srv_action_message(
					$op,
					"Driver has been uninstalled successfully!"
				)
			);
			return true;
		}
		else
		{
			main::set_action_message(
				self::get_srv_action_message(
					$op,
					"Driver could not be uninstalled!"
				)
			);
			return false;
		}
		
	}
	
	public static function setup_network(){

		if(isset(dev::$get['server_id'])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$rows = dao_servers::select(
				"WHERE server_id = :v_server_id",
				array("v_server_id"=>dev::$get['server_id'])
			);
			if(isset($rows[0])){
				$vo_server = $rows[0];
			}
			else
			{
				echo "Server not found.";
				exit;
			}
			
			//Get Server Driver
			$serverDriver = new server_operations($vo_server);
			$serverDriver->setup_network();
			$serverDriver->execute();
			
			if($serverDriver->isOkay()){
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server network has been reintialized!"
					)
				);
				return true;
			}
			else
			{
				main::set_action_message(
					self::get_srv_action_message(
						$serverDriver,
						"Server network could not be reinitialized!"
					)
				);
				return false;
			}
		}
	}
	
	public static function change_details(){

		if(isset(dev::$post["server_id"])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			if(verify_servers::details()){
				$rows = dao_servers::select(
					"WHERE server_id = :v_server_id",
					array("v_server_id"=>dev::$post['server_id'])
				);
				if(isset($rows[0])){
					$vo_server = $rows[0];
				}
				else
				{
					echo "Server not found.";
					exit;
				}
				$vo_server->populate(dev::$post);
				$vo_server->set_modified(time());

				dao_servers::update(
					$vo_server->update_array(),
					" WHERE server_id = :v_server_id ",
					array("v_server_id"=>$vo_server->get_server_id())
				);
				event_api::add_event('Server #'.$vo_server->get_server_id().' "'.$vo_server->get_name().'" was updated.');
				main::set_action_message("Server details have been updated!");
				return true;

			}
			else
			{
				return false;
			}
		}
	}

}

?>
