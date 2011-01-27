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

class ctl_update_plan {

	private $plan;
	private $fields;

	public function __construct(){

		$this->post();
		$this->get_plan();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['plan_id'])){
			func_plans::save_plan();
		}
	}

	private function get_plan(){

		if(isset(dev::$get['plan_id'])){
			$plan = func_plans::get_plan_by_id(dev::$get['plan_id']);
			$this->plan = $plan[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=plans&sec=insert");
		}
	}

	private function check_fields(){

			$this->fields['plan_id']			=		$this->plan->get_plan_id();
			$this->fields['name']				=		$this->plan->get_name();
			$this->fields['disk_space']			=		$this->plan->get_disk_space();
			$this->fields['backup_space']			=		$this->plan->get_backup_space();
			$this->fields['swap_space']			=		$this->plan->get_swap_space();
			$this->fields['g_mem']			=		$this->plan->get_g_mem();
			$this->fields['b_mem']			=		$this->plan->get_b_mem();
			$this->fields['cpu_pct']			=		$this->plan->get_cpu_pct();
			$this->fields['cpu_num']			=		$this->plan->get_cpu_num();
			$this->fields['out_bw']			=		$this->plan->get_out_bw();
			$this->fields['in_bw']			=		$this->plan->get_in_bw();
			$this->fields['created']			=		$this->plan->get_created();
			$this->fields['modified']			=		$this->plan->get_modified();
	}

	private function show_page(){

		vw_plans::title('Update Plan');
		vw_plans::form($this->fields);
	}

}

?>