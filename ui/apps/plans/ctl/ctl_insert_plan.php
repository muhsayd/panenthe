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

class ctl_insert_plan {

	private $fields;

	public function __construct(){

		$this->post();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['plan_id'])){
			func_plans::save_plan();
		}
	}

	private function check_fields(){

		$this->fields = array(
			'plan_id'			=>		"",
			'name'				=>		"",
			'disk_space'			=>		"",
			'backup_space'			=>		"",
			'swap_space'			=>		"",
			'g_mem'			=>		"",
			'b_mem'			=>		"",
			'cpu_pct'			=>		"",
			'cpu_num'			=>		"",
			'out_bw'			=>		"",
			'in_bw'			=>		"",
			'created'			=>		"",
			'modified'			=>		""
		);

		if(isset(dev::$post['plan_id'])){
			$this->fields = dev::$post;
		}
	}

	private function show_page(){

		vw_plans::title('Insert Plan');
		vw_plans::form($this->fields);
	}

}

?>