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

class vo_drivers {

	private $driver_id;
	private $ext_ref;
	private $name;
	private $version;

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

	public function set_driver_id($value){
		$this->driver_id = $value;
	}

	public function set_ext_ref($value){
		$this->ext_ref = $value;
	}

	public function set_name($value){
		$this->name = $value;
	}

	public function set_version($value){
		$this->version = $value;
	}

	public function get_driver_id(){
		return $this->driver_id;
	}

	public function get_ext_ref(){
		return $this->ext_ref;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_version(){
		return $this->version;
	}
	
	public function api_array(){
	
		$records['driver_id'] = $this->get_driver_id();
		$records["ext_ref"] = $this->get_ext_ref();
		$records["name"] = $this->get_name();
		$records["version"] = $this->get_version();
		
		return $records;
		
	}

	public function update_array(){

		$records["ext_ref"] = $this->get_ext_ref();
		$records["name"] = $this->get_name();
		$records["version"] = $this->get_version();

		return $records;

	}

	public function insert_array(){

		$records["ext_ref"] = $this->get_ext_ref();
		$records["name"] = $this->get_name();
		$records["version"] = $this->get_version();

		return $records;

	}

}

?>
