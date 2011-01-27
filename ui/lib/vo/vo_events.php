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

class vo_events {

	private $event_id;
	private $message;
	private $code;
	private $time;
	private $is_acknowledged;
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

	public function set_event_id($value){
		$this->event_id = $value;
	}

	public function set_message($value){
		$this->message = $value;
	}
	
	public function set_code($value){
		$this->code = $value;
	}

	public function set_time($value){
		$this->time = $value;
	}

	public function set_is_acknowledged($value){
		$this->is_acknowledged = $value;
	}

	public function set_created($value){
		$this->created = $value;
	}

	public function set_modified($value){
		$this->modified = $value;
	}

	public function get_event_id(){
		return $this->event_id;
	}

	public function get_message(){
		return $this->message;
	}
	
	public function get_code(){
		return $this->code;
	}

	public function get_time(){
		return $this->time;
	}

	public function get_is_acknowledged(){
		return $this->is_acknowledged;
	}

	public function get_created(){
		return $this->created;
	}

	public function get_modified(){
		return $this->modified;
	}
	
	public function api_array(){
	
		$records["event_id"] = $this->get_event_id();
		$records["message"] = $this->get_message();
		$records["code"] = $this->get_code();
		$records["time"] = $this->get_time();
		$records["is_acknowledged"] = $this->get_is_acknowledged();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();
		
		return $records;
		
	}

	public function update_array(){

		$records["message"] = $this->get_message();
		$records["code"] = $this->get_code();
		$records["time"] = $this->get_time();
		$records["is_acknowledged"] = $this->get_is_acknowledged();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function insert_array(){

		$records["message"] = $this->get_message();
		$records["code"] = $this->get_code();
		$records["time"] = $this->get_time();
		$records["is_acknowledged"] = $this->get_is_acknowledged();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

}

?>
