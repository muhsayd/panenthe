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

class vo_users {

	private $user_id;
	private $username;
	private $password;
	private $salt;
	private $email;
	private $first_name;
	private $last_name;
	private $is_staff;
	private $created;
	private $modified;
	private $last_login;
	private $last_refresh;

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

	public function set_user_id($value){
		$this->user_id = $value;
	}

	public function set_username($value){
		$this->username = $value;
	}

	public function set_password($value){
		$this->password = $value;
	}

	public function set_salt($value){
		$this->salt = $value;
	}

	public function set_email($value){
		$this->email = $value;
	}

	public function set_first_name($value){
		$this->first_name = $value;
	}

	public function set_last_name($value){
		$this->last_name = $value;
	}

	public function set_is_staff($value){
		$this->is_staff = $value;
	}
	
	public function set_last_login($value){
		$this->last_login = $value;
	}
	
	public function set_last_refresh($value){
		$this->last_refresh = $value;
	}

	public function set_created($value){
		$this->created = $value;
	}

	public function set_modified($value){
		$this->modified = $value;
	}

	public function get_user_id(){
		return $this->user_id;
	}

	public function get_username(){
		return $this->username;
	}

	public function get_password(){
		return $this->password;
	}

	public function get_salt(){
		return $this->salt;
	}

	public function get_email(){
		return $this->email;
	}

	public function get_first_name(){
		return $this->first_name;
	}

	public function get_last_name(){
		return $this->last_name;
	}

	public function get_is_staff(){
		return $this->is_staff;
	}
	
	public function get_last_login(){
		return $this->last_login;
	}
	
	public function get_last_refresh(){
		return $this->last_refresh;
	}

	public function get_created(){
		return $this->created;
	}

	public function get_modified(){
		return $this->modified;
	}

	public function update_array_no_password(){

		$records["username"] = $this->get_username();
		$records["email"] = $this->get_email();
		$records["first_name"] = $this->get_first_name();
		$records["last_name"] = $this->get_last_name();
		$records["is_staff"] = $this->get_is_staff();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}
	
	public function api_array(){

		$records["user_id"] = $this->get_user_id();
		$records["username"] = $this->get_username();
		$records["password"] = $this->get_password();
		$recores["salt"] = $this->get_salt();
		$records["email"] = $this->get_email();
		$records["first_name"] = $this->get_first_name();
		$records["last_name"] = $this->get_last_name();
		$records["last_login"] = $this->get_last_login();
		$records["last_refresh"] = $this->get_last_refresh();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}
	
	public function update_array(){

		$records["username"] = $this->get_username();
		$records["password"] = $this->get_password();
		$records["salt"] = $this->get_salt();
		$records["email"] = $this->get_email();
		$records["first_name"] = $this->get_first_name();
		$records["last_name"] = $this->get_last_name();
		$records["is_staff"] = $this->get_is_staff();
		$records["last_login"] = $this->get_last_login();
		$records["last_refresh"] = $this->get_last_refresh();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function insert_array(){

		$records["username"] = $this->get_username();
		$records["password"] = $this->get_password();
		$records["salt"] = $this->get_salt();
		$records["email"] = $this->get_email();
		$records["first_name"] = $this->get_first_name();
		$records["last_name"] = $this->get_last_name();
		$records["is_staff"] = $this->get_is_staff();
		$records["last_login"] = $this->get_last_login();
		$records["last_refresh"] = $this->get_last_refresh();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

}

?>
