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

class ctl_vps_home {

	private $is_show_home = true;
	private $vo_vps;
	private $power_control = false;
	
	public function __construct(){

		//Get VPS
		$this->get_vps();
		
		//Check Suspension
		$this->check_suspension();
		$this->check_lock();

		//VPS Mods
		$this->limits();
		$this->rebuild();
		$this->name();
		$this->root_password();
		$this->user_beancounts();

		//VPS Actions
		$this->start();
		$this->reboot();
		$this->stop();
		$this->poweroff();
		$this->suspension();
		$this->create_ost();
		$this->resend_welcome_email();

		//VPS Home
		$this->show_home();
	}

	private function get_vps(){

		$vps = func_vps::get_vps_by_id(dev::$get['vps_id']);

		if(isset($vps[0])){
			$this->vo_vps = $vps[0];
			dev::$tpl->set_constant("vps_id",$this->vo_vps->get_vps_id());
			dev::$tpl->set_constant("vps_name",$this->vo_vps->get_name());
		}
		else
		{
			main::error_page("VPS not found.");
		}
		
	}
	
	private function check_suspension(){
	
		if(!main::$is_staff && $this->vo_vps->get_is_suspended() == '1'){
			
			dev::$tpl->parse(
				'vps',
				'client_suspended',
				$this->vo_vps->home_array()
			);
			
			$this->is_show_home = false;
			
		}
		
	}
	
	private function check_lock(){
	
		if($this->vo_vps->get_is_locked() == '1'){
			
			main::notice_page(
				'The VM has been locked waiting for a background '.
				'operation to complete. Please check back soon.'
			);
			$this->is_show_home = false;
			
		}
		
	}

	private function limits(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'limits'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'change_limits'){
				func_vps::change_limits();
				$this->get_vps();
			}
			
			$this->is_show_home = false;
			
			//Get Limits
			$limits = array();
			$limits['disk_space'] = func_vps::hr_size($this->vo_vps->get_disk_space(),'mb');
			$limits['backup_space'] = func_vps::hr_size($this->vo_vps->get_backup_space(),'mb');
			$limits['swap_space'] = func_vps::hr_size($this->vo_vps->get_swap_space(),'mb');
			$limits['g_mem'] = func_vps::hr_size($this->vo_vps->get_g_mem(),'mb');
			$limits['b_mem'] = func_vps::hr_size($this->vo_vps->get_b_mem(),'mb');
			$limits['in_bw'] = func_vps::hr_size($this->vo_vps->get_in_bw(),'gb');
			$limits['out_bw'] = func_vps::hr_size($this->vo_vps->get_out_bw(),'gb');
			
			$fields['hostname']		=	$this->vo_vps->get_hostname();
			$fields['vps_id']		=	$this->vo_vps->get_vps_id();
			$fields['disk_space']	=	$limits['disk_space'][0];
			$fields['backup_space']	=	$limits['backup_space'][0];
			$fields['swap_space']	=	$limits['swap_space'][0];
			$fields['g_mem']		=	$limits['g_mem'][0];
			$fields['b_mem']		=	$limits['b_mem'][0];
			$fields['cpu_pct']		=	$this->vo_vps->get_cpu_pct();
			$fields['cpu_num']		=	$this->vo_vps->get_cpu_num();
			$fields['in_bw']		=	$limits['in_bw'][0];
			$fields['out_bw']		=	$limits['out_bw'][0];
			
			//Get Plan Information
			$fields['plan_id'] = func_vps::get_plans("plan_id","");
			$fields['plan_information'] 	=		func_vps::get_plan_information();

			vw_vps::limits_form($fields);

		}
	}

	private function rebuild(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'rebuild'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'rebuild'){
				func_vps::change_rebuild();
				$this->is_show_home = true;
			}
			else
			{
				$this->is_show_home = false;

				$fields['vps_id']				=		$this->vo_vps->get_vps_id();
				$fields['hostname']				=		$this->vo_vps->get_hostname();
				$fields['ost'] = func_vps::get_ost("ost",$this->vo_vps->get_ost());

				vw_vps::rebuild_form($fields);
			}

		}
	}
	
	private function name(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'name'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'name'){
				func_vps::change_name();
				$this->get_vps();
			}
			
			$this->is_show_home = false;

			$fields['vps_id']	=	$this->vo_vps->get_vps_id();
			$fields['name']		=	$this->vo_vps->get_name();
			$fields['hostname']	=	$this->vo_vps->get_hostname();
			$fields['ost'] 		= 	func_vps::get_ost("ost",$this->vo_vps->get_ost());

			vw_vps::name_form($fields);


		}
	}

	private function root_password(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'root_password'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'root_password'){
				func_vps::change_root_password();
				$this->is_show_home = true;
			}
			else
			{
				$this->is_show_home = false;
				$fields['password_message']		=		'leave blank to generate';
				$fields['hostname']				=		$this->vo_vps->get_hostname();
				$fields['vps_id']				=		$this->vo_vps->get_vps_id();
				vw_vps::root_password_form($fields);
			}

		}
	}
	
	private function user_beancounts(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'user_beancounts'){
			$this->is_show_home = false;
			$beancount_rows = func_vps::get_stats_beancounts($this->vo_vps);
			vw_vps::user_beancounts($this->vo_vps,$beancount_rows);
		}
	}
	
	private function suspension(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'suspension'){
			$vpsDriver = new vps_operations($this->vo_vps);
			if($this->vo_vps->get_is_suspended() == '0'){
				func_vps::suspension($this->vo_vps,'1');
				$vpsDriver->suspend(func_vps_ip::get_all_ips($this->vo_vps));
				$action = 'suspended';
				$action_pl = 'suspension';
			}
			else
			{
				func_vps::suspension($this->vo_vps,'0');
				$vpsDriver->unsuspend(func_vps_ip::get_all_ips($this->vo_vps));
				$action = 'unsuspended';
				$actoin_pl = 'unsuspension';
			}
			$vpsDriver->execute();
			if($vpsDriver->isOkay()){
				main::set_action_message("VPS has been ".$action."!");
				$this->get_vps();
			}
			else
			{
				main::set_action_message($vpsDriver->getOutput()."<br />VPS has failed ".$action_pl."!");
			}
		}
	}
	
	private function create_ost(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'create_ost'){
			if(isset(dev::$post['action']) && dev::$post['action'] == 'create_ost'){
				func_vps::create_ost();
				$this->is_show_home = true;
			}
			else
			{
				$this->is_show_home = false;

				$fields['vps_id']	=	$this->vo_vps->get_vps_id();
				$fields['hostname']	=	$this->vo_vps->get_hostname();
				
				$rows = func_ost::get_ost_by_id($this->vo_vps->get_ost());
				if(!isset($rows[0])){
					main::error_page("No OST Found.");
				}
				$vo_ost = $rows[0];
				$fields['name'] 	= 	$vo_ost->get_name();
				$fields['name_ext']	=	$this->vo_vps->get_hostname();
				$fields['path']		=	$vo_ost->get_path();
				$fields['arch']		=	$vo_ost->get_arch();
				$fields['driver_id']=	$vo_ost->get_driver_id();

				vw_vps::create_ost_form($fields);
			}

		}
	}
	
	private function resend_welcome_email(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'resend_welcome_email'){
			func_vps::resend_welcome_email($this->vo_vps);
			main::set_action_message("Welcome email has been resent!");
		}
	}

	private function start(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'start'){
			
			//Mark Power Control Mode			
			$this->power_control = true;
			
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
		
			//Mark Power Control Mode			
			$this->power_control = true;
			
			$vpsDriver = new vps_operations($this->vo_vps);
			$vpsDriver->reboot_vps();
			$vpsDriver->execute();
			if($vpsDriver->isOkay()){
				main::set_action_message("VPS has been rebooted!");
			}
			else
			{
				main::set_action_message($vpsDriver->getOutput()."<br />VPS has failed to reboot!");
			}
		}
	}

	private function stop(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'stop'){
		
			//Mark Power Control Mode			
			$this->power_control = true;
			
			$vpsDriver = new vps_operations($this->vo_vps);
			$vpsDriver->stop_vps();
			$vpsDriver->execute();
			if($vpsDriver->isOkay()){
				main::set_action_message("VPS has been shutdown!");
			}
			else
			{
				main::set_action_message("VPS has failed to shutdown!");
			}
		}
	}

	private function poweroff(){
		if(isset(dev::$get['act']) && dev::$get['act'] == 'poweroff'){
		
			//Mark Power Control Mode			
			$this->power_control = true;
			
			$vpsDriver = new vps_operations($this->vo_vps);
			$vpsDriver->poweroff_vps();
			$vpsDriver->execute();
			if($vpsDriver->isOkay()){
				main::set_action_message("VPS has been powered off!");
			}
			else
			{
				main::set_action_message($vpsDriver->getOutput()."<br />VPS has failed to power off!");
			}
		}
	}

	private function show_home(){

		if($this->is_show_home){
			
			$vpsDriver = new vps_operations($this->vo_vps);
			
			//Update Stats
			if(!$this->power_control){
				$status = $vpsDriver->stats_vps();
			} else {
				$status = $vpsDriver->status_vps();
			}
			
			//Get Stats
			list($stats,$limits) = func_vps::get_stats($this->vo_vps,$status['code']);
			
			//Get OST
			$ost = func_ost::get_ost_by_id($this->vo_vps->get_ost());
			$ost = $ost[0];
			$this->vo_vps->set_ost($ost->get_name());
			
			//Get Server
			$server = func_servers::get_server_by_id($this->vo_vps->get_server_id());
			$server = $server[0];

			//Get Driver
			$driver = dao_drivers::get_by_driver_id($this->vo_vps->get_driver_id());
			
			vw_vps::vps_home($this->vo_vps,$status,$stats,$limits,$server,$driver);
		}
		
	}

}

?>
