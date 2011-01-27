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

class ctl_update_ip_pool {

	private $ip_pool;
	private $fields;

	public function __construct(){

		$this->post();
		$this->get_ip_pool();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['ip_pool_id'])){
			func_ip_pools::save_ip_pool();
		}
	}

	private function get_ip_pool(){

		if(isset(dev::$get['ip_pool_id'])){
			$ip_pool = func_ip_pools::get_ip_pool_by_id(dev::$get['ip_pool_id']);
			$this->ip_pool = $ip_pool[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=ip_pools&sec=insert");
		}
	}

	private function check_fields(){

			$this->fields['name']				=		$this->ip_pool->get_name();
			$this->fields['ip_pool_id']			=		$this->ip_pool->get_ip_pool_id();
			$this->fields['first_ip']			=		$this->ip_pool->get_first_ip();
			$this->fields['last_ip']			=		$this->ip_pool->get_last_ip();
			$this->fields['dns']			=		$this->ip_pool->get_dns();
			$this->fields['gateway']			=		$this->ip_pool->get_gateway();
			$this->fields['netmask']			=		$this->ip_pool->get_netmask();
			$this->fields['created']			=		$this->ip_pool->get_created();
			$this->fields['modified']			=		$this->ip_pool->get_modified();

			$this->fields['servers']			=		func_ip_pools::get_servers('servers[]',func_ip_pools::get_mapped_servers($this->ip_pool));
	}

	private function show_page(){

		vw_ip_pools::title('Update Ip Pool');
		vw_ip_pools::form($this->fields);
	}

}

?>