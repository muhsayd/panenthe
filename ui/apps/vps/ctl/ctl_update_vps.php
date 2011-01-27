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

class ctl_update_vps {

	private $vps;
	private $fields;

	public function __construct(){

		$this->post();
		$this->get_vps();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['action']) && dev::$post['action'] == 'change_limits'){
			func_vps::change_limits();
		}
		
	}

	private function get_vps(){

		if(isset(dev::$get['vps_id'])){
			$vps = func_vps::get_vps_by_id(dev::$get['vps_id']);
			$this->vps = $vps[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=vps&sec=insert");
		}
	}

	private function check_fields(){

			$this->fields['vps_id']			=		$this->vps->get_vps_id();
			$this->fields['hostname']			=		$this->vps->get_hostname();
			$this->fields['name']				=		$this->vps->get_name();
			$this->fields['real_id']			=		$this->vps->get_real_id();
			$this->fields['disk_space']			=		$this->vps->get_disk_space();
			$this->fields['backup_space']			=		$this->vps->get_backup_space();
			$this->fields['swap_space']			=		$this->vps->get_swap_space();
			$this->fields['g_mem']			=		$this->vps->get_g_mem();
			$this->fields['b_mem']			=		$this->vps->get_b_mem();
			$this->fields['cpu_pct']			=		$this->vps->get_cpu_pct();
			$this->fields['cpu_num']			=		$this->vps->get_cpu_num();
			$this->fields['in_bw']			=		$this->vps->get_in_bw();
			$this->fields['out_bw']			=		$this->vps->get_out_bw();
			$this->fields['ost']			=		$this->vps->get_ost();
			$this->fields['is_running']			=		$this->vps->get_is_running();
			$this->fields['created']			=		$this->vps->get_created();
			$this->fields['modified']			=		$this->vps->get_modified();
	}

	private function show_page(){

		vw_vps::title('Update VM Limits');
		vw_vps::form($this->fields);
	}

}

?>
