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

class ctl_view_ip_pool {

	private $ip_pool;
	private $ip_map;

	public function __construct(){
		$this->get_ip_pool();
		$this->get_ip_map();
		$this->show_page();
	}

	private function get_ip_pool(){

		if(isset(dev::$get['ip_pool_id'])){
			$ip_pool = func_ip_pools::get_ip_pool_by_id(dev::$get['ip_pool_id']);
			$this->ip_pool = $ip_pool[0];
		}
		else
		{
			main::error_page("IP Pool not found.");
		}
	}
	
	private function get_ip_map(){
		
		$this->ip_map = dev::$db->query("
			SELECT * FROM ip_map 
			WHERE ip_pool_id = '".$this->ip_pool->get_ip_pool_id()."' 
		");
		
		$this->ip_map = $this->ip_map->fetchAll();
		
		foreach($this->ip_map AS $result){
			
			$this->vps[$result['vps_id']] = func_vps::get_vps_by_id($result['vps_id']);
		
		}
		
	}

	private function show_page(){
	
		vw_ip_pools::title('View Ip Pool');
		vw_ip_pools::view_ip_pool($this->ip_map,$this->ip_pool,$this->vps);

	}

}

?>
