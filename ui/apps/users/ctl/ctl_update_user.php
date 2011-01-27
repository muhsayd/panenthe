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

class ctl_update_user {

	private $user;
	private $fields;

	public function __construct(){
		
		$this->post();
		$this->get_user();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['user_id'])){
			func_users::save_user();
		}
	}

	private function get_user(){

		if(isset(dev::$get['user_id'])){
			$user = func_users::get_user_by_id(dev::$get['user_id']);
			$this->user = $user[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=users&sec=insert");
		}
	}

	private function check_fields(){

			$this->fields['user_id']			=		$this->user->get_user_id();
			$this->fields['username']			=		$this->user->get_username();
			$this->fields['password']			=		$this->user->get_password();
			$this->fields['salt']			=		$this->user->get_salt();
			$this->fields['email']			=		$this->user->get_email();
			$this->fields['first_name']			=		$this->user->get_first_name();
			$this->fields['last_name']			=		$this->user->get_last_name();
			$this->fields['is_staff']			=		$this->user->get_is_staff();
			$this->fields['created']			=		$this->user->get_created();
			$this->fields['modified']			=		$this->user->get_modified();

			if($this->user->get_is_staff() == "1"){
				$this->fields['is_staff'] = 'selected="selected"';
				$this->fields['is_not_staff'] = '';
			}
			else
			{
				$this->fields['is_staff'] = '';
				$this->fields['is_not_staff'] = 'selected="selected"';
			}
			
	}

	private function show_page(){

		vw_users::title('Update User');
		vw_users::form($this->fields);
	}

}

?>
