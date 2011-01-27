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

class ctl_login{

	private $action_message;
	private $skip_sec = false;

	public function __construct(){
		$this->handle_post();
		$this->handle_sec();
	}

	private function handle_post(){
	
		//Forgot Password
		if(isset(dev::$post['forgot_password'])){
			if(!func_login::do_forgot_password()){
				$this->action_message = func_login::get_action_message();
				$this->show_forgot_password();
				$this->skip_sec = true;
				return;
			}
			else
			{
				$this->show_forgot_password_confirmation();
			}
		}

		//User Login
		if(isset(dev::$post['staff_login'])){
			if(!func_login::do_login()){
				$this->action_message = func_login::get_action_message();
			}
			else
			{
				header("Location: ".main::$url.'/index.php');
				exit;
			}
		}
	
	}

	private function handle_sec(){
		
		if($this->skip_sec){
			$this->output_page();
			return;
		}
		
		if(isset(dev::$get['sec']) && dev::$get['sec'] == 'forgot_password'){
			$this->show_forgot_password();
		}
		else
		{
			 $this->show_login();
		}
		$this->output_page();
		
	}

	private function show_login(){
		vw_login::load_js();
		vw_login::load_css();
		vw_login::login($this->action_message);
	}

	private function show_forgot_password(){
		vw_login::load_js();
		vw_login::load_css();
		vw_login::forgot_password($this->action_message);
	}

	private function show_forgot_password_confirmation(){
		vw_login::load_js();
		vw_login::load_css();
		vw_login::forgot_password_confirmation();
		$this->output_page();
		exit;
	}

	private function output_page(){
		main::output();
	}

}

?>
