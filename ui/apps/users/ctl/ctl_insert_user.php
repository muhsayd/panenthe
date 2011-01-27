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

class ctl_insert_user {

	private $fields;

	public function __construct(){
		main::check_permissions('insert_user');
		$this->post();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['user_id'])){
			func_users::save_user();
		}
	}

	private function check_fields(){

		$this->fields = array(
			'user_id'			=>		"",
			'username'			=>		"",
			'password'			=>		"",
			'salt'			=>		"",
			'email'			=>		"",
			'first_name'			=>		"",
			'last_name'			=>		"",
			'is_staff'			=>		"",
			'created'			=>		"",
			'modified'			=>		""
		);

		if(isset(dev::$post['user_id'])){
			$this->fields = dev::$post;
		}
		$this->fields['email'] = '';
	}

	private function show_page(){

		vw_users::title('Insert User');
		vw_users::form($this->fields,true);
	}

}

?>
