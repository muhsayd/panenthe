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

class ctl_insert_ip_pool {

	private $fields;

	public function __construct(){

		$this->post();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['ip_pool_id'])){
			func_ip_pools::save_ip_pool();
		}
	}

	private function check_fields(){

		$this->fields = array(
			'name'				=>	"",
			'ip_pool_id'			=>		"",
			'first_ip'			=>		"",
			'last_ip'			=>		"",
			'dns'			=>		"",
			'gateway'			=>		"",
			'netmask'			=>		"",
			"servers"			=>		array(),
			'created'			=>		"",
			'modified'			=>		""
		);

		if(isset(dev::$post['ip_pool_id'])){
			$this->fields = dev::$post;
		}

		$this->fields['servers'] = func_ip_pools::get_servers('servers[]',$this->fields['servers']);
	}

	private function show_page(){

		vw_ip_pools::title('Insert Ip Pool');
		vw_ip_pools::form($this->fields);
	}

}

?>