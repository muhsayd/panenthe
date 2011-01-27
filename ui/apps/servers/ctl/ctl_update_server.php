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

class ctl_update_server {

	private $server;
	private $fields;

	public function __construct(){

		$this->post();
		$this->get_server();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['server_id'])){
			func_servers::save_server();
		}
	}

	private function get_server(){

		if(isset(dev::$get['server_id'])){
			$server = func_servers::get_server_by_id(dev::$get['server_id']);
			$this->server = $server[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=servers&sec=insert");
		}
	}

	private function check_fields(){

			$this->fields['server_id']			=		$this->server->get_server_id();
			$this->fields['parent_server_id']	=		$this->server->get_parent_server_id();
			$this->fields['name']				=		$this->server->get_name();
			$this->fields['datacenter']			=		$this->server->get_datacenter();
			$this->fields['ip']					=		$this->server->get_ip();
			$this->fields['hostname']			=		$this->server->get_hostname();
			$this->fields['port']				=		$this->server->get_port();
			$this->fields['password']			=		$this->server->get_password();
			$this->fields['created']			=		$this->server->get_created();
			$this->fields['modified']			=		$this->server->get_modified();

			$this->fields['parent_server_id'] = func_servers::get_parent_servers('parent_server_id',$this->fields['parent_server_id']);
	}

	private function show_page(){

		vw_servers::title('Update Server');
		vw_servers::form($this->fields);
	}

}

?>
