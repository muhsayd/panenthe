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

class vo_ost {

	private $ost_id;
	private $name;
	private $path;
	private $driver_id;
	private $arch;
	private $os_type;

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

	public function set_ost_id($value){
		$this->ost_id = $value;
	}

	public function set_name($value){
		$this->name = $value;
	}

	public function set_path($value){
		$this->path = $value;
	}

	public function set_driver_id($value){
		$this->driver_id = $value;
	}

	public function set_arch($value){
		$this->arch = $value;
	}
	
	public function set_os_type($value){
		$this->os_type = $value;
	}

	public function get_ost_id(){
		return $this->ost_id;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_path(){
		return $this->path;
	}

	public function get_driver_id(){
		return $this->driver_id;
	}

	public function get_arch(){
		return $this->arch;
	}
	
	public function get_os_type(){
		return $this->os_type;
	}
	
	public function api_array(){
	
		$records['ost_id'] = $this->get_ost_id();
		$records["name"] = $this->get_name();
		$records["path"] = $this->get_path();
		$records["driver_id"] = $this->get_driver_id();
		$records['arch'] = $this->get_arch();
		$records['os_type'] = $this->get_os_type();
		
		return $records;
		
	}

	public function update_array(){

		$records["name"] = $this->get_name();
		$records["path"] = $this->get_path();
		$records["driver_id"] = $this->get_driver_id();
		$records['arch'] = $this->get_arch();
		$records['os_type'] = $this->get_os_type();

		return $records;

	}

	public function insert_array(){

		$records["name"] = $this->get_name();
		$records["path"] = $this->get_path();
		$records["driver_id"] = $this->get_driver_id();
		$records['arch'] = $this->get_arch();
		$records['os_type'] = $this->get_os_type();

		return $records;

	}

}

?>
