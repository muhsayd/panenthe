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

class ctl_insert_ips {

	private $fields;
	private $vo_vps;
	private $show_form = true;
	
	public function __construct(){
		$this->get_vo_vps();
		$this->set_constants();
		$this->post();
		$this->check_fields();
		$this->show_page();
	}
	
	private function get_vo_vps(){
	
		$vps = func_vps::get_vps_by_id(dev::$get['vps_id']);

		if(isset($vps[0])){
			$this->vo_vps = $vps[0];
		}
		else
		{
			echo "VPS not found.";
			exit;
		}
		
	}
	
	private function set_constants(){
		dev::$tpl->set_constant("hostname",$this->vo_vps->get_hostname());
	}

	private function post(){

		if(isset(dev::$post['action']) && dev::$post['action'] == 'add_ips'){
			func_vps_ip::add_post_ips();
			$this->show_form = false;
			new ctl_browse_ips();
		}

	}

	private function check_fields(){

		$this->fields = array();
		$this->fields['no_ips'] = '1';
		$this->fields['manual_ips'] = '';
		
		if(is_array(dev::$post)){
			$this->fields = array_merge($this->fields,dev::$post);
		}
		
	}

	private function show_page(){

		//vw_ips::title('Add IP Address(es) for VPS ID: '.dev::$get['vps_id']);
		if($this->show_form){
			vw_ips::form($this->fields);
		}
		
	}

}

?>
