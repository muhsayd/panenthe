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

class vo_ip_pools {

	private $ip_pool_id;
	private $name;
	private $first_ip;
	private $last_ip;
	private $dns;
	private $gateway;
	private $netmask;
	private $created;
	private $modified;

	public function __construct($populate_array=array()){
		$this->populate($populate_array);
	}

	public function populate($records=array()){
		foreach($records AS $col => $value){
			$function_name = "set_".$col;
			if(method_exists($this,$function_name)){
				$this->$function_name($value);
			}
		}

	}

	public function set_ip_pool_id($value){
		$this->ip_pool_id = $value;
	}

	public function set_name($value){
		$this->name = $value;
	}

	public function set_first_ip($value){
		$this->first_ip = $value;
	}

	public function set_last_ip($value){
		$this->last_ip = $value;
	}

	public function set_dns($value){
		$this->dns = $value;
	}

	public function set_gateway($value){
		$this->gateway = $value;
	}

	public function set_netmask($value){
		$this->netmask = $value;
	}

	public function set_created($value){
		$this->created = $value;
	}

	public function set_modified($value){
		$this->modified = $value;
	}

	public function get_ip_pool_id(){
		return $this->ip_pool_id;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_first_ip(){
		return $this->first_ip;
	}

	public function get_last_ip(){
		return $this->last_ip;
	}

	public function get_dns(){
		return $this->dns;
	}

	public function get_gateway(){
		return $this->gateway;
	}

	public function get_netmask(){
		return $this->netmask;
	}

	public function get_created(){
		return $this->created;
	}

	public function get_modified(){
		return $this->modified;
	}

	public function api_array(){

		$records['ip_pool_id'] = $this->get_ip_pool_id();
		$records["name"] = $this->get_name();
		$records["first_ip"] = $this->get_first_ip();
		$records["last_ip"] = $this->get_last_ip();
		$records["dns"] = $this->get_dns();
		$records["gateway"] = $this->get_gateway();
		$records["netmask"] = $this->get_netmask();

		return $records;

	}
	
	public function update_array(){

		$records["name"] = $this->get_name();
		$records["first_ip"] = $this->get_first_ip();
		$records["last_ip"] = $this->get_last_ip();
		$records["dns"] = $this->get_dns();
		$records["gateway"] = $this->get_gateway();
		$records["netmask"] = $this->get_netmask();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function insert_array(){

		$records["name"] = $this->get_name();
		$records["first_ip"] = $this->get_first_ip();
		$records["last_ip"] = $this->get_last_ip();
		$records["dns"] = $this->get_dns();
		$records["gateway"] = $this->get_gateway();
		$records["netmask"] = $this->get_netmask();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

}

?>
