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

class ctl_insert_au {

	private $fields;
	private $vo_vps;

	public function __construct(){
		$this->post();
		$this->get_vo_vps();
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

	private function post(){

		if(isset(dev::$post['action']) && dev::$post['action'] == 'add_au'){
			func_vps_au::add_post_au();
		}

	}

	private function check_fields(){

		$this->fields = array(
			"users"		=>	func_vps_au::get_users_select(),
			"hostname"	=>	$this->vo_vps->get_hostname()
		);
		
	}

	private function show_page(){

		vw_au::title('Add Assigned User for VPS ID: '.dev::$get['vps_id']);
		vw_au::form($this->fields);
	}

}

?>
