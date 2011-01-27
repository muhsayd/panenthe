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

class ctl_update_ost {

	private $ost;
	private $fields;

	public function __construct(){

		$this->post();
		$this->get_ost();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['ost_id'])){
			func_ost::save_ost();
		}
	}

	private function get_ost(){

		if(isset(dev::$get['ost_id'])){
			$ost = func_ost::get_ost_by_id(dev::$get['ost_id']);
			$this->ost = $ost[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=ost&sec=insert");
		}
	}

	private function check_fields(){

		$this->fields['ost_id']			=		$this->ost->get_ost_id();
		$this->fields['name']			=		$this->ost->get_name();
		$this->fields['path']			=		$this->ost->get_path();
		$this->fields['os_type']		=		$this->ost->get_os_type();
		$this->fields['driver_id']		=		$this->ost->get_driver_id();
		$this->fields['arch']			=		$this->ost->get_arch();

		$this->fields['os_type'] = func_ost::get_os_type_drop('os_type',$this->fields['os_type']);
		$this->fields['driver_id'] = func_ost::get_drivers('driver_id',$this->fields['driver_id']);
		$this->fields['arch'] = func_ost::get_arch('arch',$this->fields['arch']);
		
	}

	private function show_page(){

		vw_ost::title('Update Ost');
		vw_ost::form($this->fields);
	}

}

?>
