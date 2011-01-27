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

class ctl_insert_vps {

	private $fields;

	public function __construct(){

		$this->post();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['action']) && dev::$post['action'] == 'create_vps'){
			func_vps::create_post_vps();
		}

	}

	private function check_fields(){

		$this->fields = array(
			'vps_id'			=>		'',
			'server_id'			=>		'',
			'root_password'		=>		'',
			'hostname'			=>		'',
			'name'				=>		'',
			'user_add_type'		=>		'',
			'add_user'			=>		'',
			'assign_user'		=>		'',
			'user_id'			=>		'',
			'username'			=>		'',
			'password_message'	=>		'leave blank to auto generate',
			'email'				=>		'',
			'first_name'		=>		'',
			'last_name'			=>		'',
			'welcome_email'		=>		'',
			'real_id'			=>		'',
			'no_ips'			=>		'1',
			'manual_ips'		=>		'',
			'plan_id'			=>		'',
			'disk_space'		=>		'',
			'backup_space'		=>		'',
			'swap_space'		=>		'',
			'g_mem'				=>		'',
			'b_mem'				=>		'',
			'cpu_pct'			=>		'',
			'cpu_num'			=>		'',
			'in_bw'				=>		'',
			'out_bw'			=>		'',
			'ost'				=>		'',
			'is_running'		=>		'',
			'driver_id'			=>		'',
			'created'			=>		'',
			'modified'			=>		''
		);

		if(isset(dev::$post['vps_id'])){
			$this->fields = array_merge($this->fields,dev::$post);
		}

		if($this->fields['user_add_type'] == 'add_user'){
			$this->fields['add_user'] = 'selected="selected"';
		}

		if($this->fields['user_add_type'] == 'assign_user'){
			$this->fields['assign_user'] = 'selected="selected"';
		}

		if(isset(dev::$post['name']) && !isset(dev::$post['welcome_email'])){
			$this->fields['welcome_email'] = '';
		}
		elseif(isset(dev::$post['name']) && isset(dev::$post['welcome_email'])){
			$this->fields['welcome_email'] = 'checked="checked"';
		} else {
			$this->fields['welcome_email'] = 'checked="checked"';
		}

		$this->fields['driver_id'] = func_vps::get_drivers("driver_id",$this->fields['driver_id']);
		$this->fields['ost'] = func_vps::get_ost("ost",$this->fields['ost']);
		$this->fields['server_id'] = func_vps::get_servers("server_id",$this->fields['server_id']);
		$this->fields['plan_id'] = func_vps::get_plans("plan_id",$this->fields['plan_id']);

		$this->fields['plan_information'] = func_vps::get_plan_information();

		$this->fields['user_id'] = func_vps_au::get_users_select($this->fields['user_id']);

		if(isset(dev::$post['create_vps_step1'])){

			$server = dao_servers::get_by_server_id(dev::$post['server_id']);
			$driver = dao_drivers::get_by_driver_id($server->get_driver_id());

			$this->fields['server'] = $server->get_name();
			$this->fields['server_id'] = $server->get_server_id();
			$this->fields['driver'] = $driver->get_name();
			$this->fields['driver_id'] = $driver->get_driver_id();
			$this->fields['ost'] = func_vps::get_ost("ost",$this->fields['ost'],$server);
			$this->fields['kernel'] = func_servers::get_kernel_images("kernel",$this->fields,$server);

		}

	}

	private function show_page(){

		if(!isset(dev::$post['create_vps_step1']) && !isset(dev::$post['action'])){
			vw_vps::form_step1($this->fields);
		} else {

			if(!func_vps::$create_confirm){
				vw_vps::title('Insert VPS');
				vw_vps::form($this->fields);
			}

		}

	}

}

?>
