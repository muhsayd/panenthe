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

class vo_servers {

	private $server_id;
	private $parent_server_id;
	private $driver_id;
	private $name;
	private $datacenter;
	private $ip;
	private $hostname;
	private $port;
	private $password;
	private $is_locked;
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

	public function set_server_id($value){
		$this->server_id = $value;
	}

	public function set_parent_server_id($value){
		$this->parent_server_id = $value;
	}
	
	public function set_driver_id($value){
		$this->driver_id = $value;
	}

	public function set_name($value){
		$this->name = $value;
	}
	
	public function set_datacenter($value){
		$this->datacenter = $value;
	}

	public function set_ip($value){
		$this->ip = $value;
	}

	public function set_hostname($value){
		$this->hostname = $value;
	}

	public function set_port($value){
		$this->port = $value;
	}

	public function set_password($value){
		$this->password = $value;
	}
	
	public function set_is_locked($value){
		$this->is_locked = $value;
	}

	public function set_created($value){
		$this->created = $value;
	}

	public function set_modified($value){
		$this->modified = $value;
	}

	public function get_server_id(){
		return $this->server_id;
	}

	public function get_parent_server_id(){
		return $this->parent_server_id;
	}
	
	public function get_driver_id(){
		return $this->driver_id;
	}

	public function get_name(){
		return $this->name;
	}
	
	public function get_datacenter(){
		return $this->datacenter;
	}

	public function get_ip(){
		return $this->ip;
	}

	public function get_hostname(){
		return $this->hostname;
	}

	public function get_port(){
		return $this->port;
	}

	public function get_password(){
		return $this->password;
	}
	
	public function get_is_locked(){
		return $this->is_locked;
	}

	public function get_created(){
		return $this->created;
	}

	public function get_modified(){
		return $this->modified;
	}
	
	public function api_array(){

		$records['server_id'] = $this->get_server_id();
		$records['parent_server_id'] = $this->get_parent_server_id();
		$records['driver_id'] = $this->get_driver_id();
		$records['name'] = $this->get_name();
		$records['datacenter'] = $this->get_datacenter();
		$records["ip"] = $this->get_ip();
		$records["hostname"] = $this->get_hostname();
		$records["port"] = $this->get_port();
		$records["password"] = $this->get_password();
		$records["is_locked"] = $this->get_is_locked();
		$records["modified"] = $this->get_modified();
		$records["created"] = $this->get_created();

		return $records;

	}

	public function update_array(){

		$records['parent_server_id'] = $this->get_parent_server_id();
		$records['driver_id'] = $this->get_driver_id();
		$records['name'] = $this->get_name();
		$records['datacenter'] = $this->get_datacenter();
		$records["ip"] = $this->get_ip();
		$records["hostname"] = $this->get_hostname();
		$records["port"] = $this->get_port();
		$records["password"] = $this->get_password();
		$records["is_locked"] = $this->get_is_locked();
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function insert_array(){

		$records['parent_server_id'] = $this->get_parent_server_id();
		$records['driver_id'] = $this->get_driver_id();
		$records['name'] = $this->get_name();
		$records['datacenter'] = $this->get_datacenter();
		$records["ip"] = $this->get_ip();
		$records["hostname"] = $this->get_hostname();
		$records["port"] = $this->get_port();
		$records["password"] = $this->get_password();
		$records["is_locked"] = $this->get_is_locked();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

}

?>
