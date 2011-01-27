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

class func_login {

	static $action_message;
	static $current_admin;

	public static function do_login(){

		$staff_member = self::get_staff_member(dev::$post['staff_login']);
		if(self::check_login_attempts()){
			if($staff_member !== false){
				if(self::validate_password($staff_member)){
					event_api::add_event('User #'.$staff_member->get_user_id().' "'.$staff_member->get_username().'" logged in.');
					self::set_staff_session($staff_member);
					return true;
				} else {
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

	}
	
	public static function do_forgot_password(){

		$staff_member = self::get_staff_member(dev::$post['staff_login']);
		if($staff_member !== false){
		
			$vo_users = $staff_member;
			
			if($vo_users->get_is_staff() > 0){
				self::$action_message = "Staff members passwords cannot be reset.";
				return false;
			}
			
			//Reset Password
			$salt = substr(md5(time().rand(1,100)),0,12);
			$password = substr(md5(time().rand(1,100)),0,12);
			$vo_users->set_password(md5($password.$salt));
			$vo_users->set_salt($salt);
			
			dao_users::update(
				$vo_users->update_array(),
				" WHERE user_id = :v_user_id ",
				array("v_user_id"=>$vo_users->get_user_id())
			);
			
			//Email User New Password
			$message = dev::$tpl->parse(
				'login',
				'password_reset_email',
				array(
					"user_id"		=>	$vo_users->get_user_id(),
					"username"		=>	$vo_users->get_username(),
					"password"		=>	$password,
					"first_name"	=>	$vo_users->get_first_name(),
					"last_name"		=>	$vo_users->get_last_name(),
					"site_url"		=>	main::$url,
					"site_name"		=>	dev::$tpl->get_constant('site_name')
				),
				true
			);
			
			dev::$mail->sendMail(array(
				"To"		=>	$vo_users->get_email(),
				"Subject"	=>	$vo_users->get_username()." your password has been reset!",
				"Message"	=>	$message
			));
			
			event_api::add_event('User #'.$staff_member->get_user_id().' "'.$staff_member->get_username().'" reset his/her password.');
			
			return true;
			
		}
		else
		{
			return false;
		}

	}

	public static function do_logout(){
		self::kill_staff_session();
		return true;
	}

	public static function set_action_message($message){
		self::$action_message = $message;
	}

	public static function get_action_message(){
		return self::$action_message;
	}

	public static function validate_login(){
		if(isset(dev::$session[main::$cnf['session_name']]['admin']['staff_login'])){
			$session = dev::$session[main::$cnf['session_name']]['admin'];
			$staff_member = self::get_staff_member($session['staff_login']);
			if($staff_member !== false){
				if($session['staff_password'] == $staff_member->get_password()){

					//Ping Last Login
					dev::$db->exec("
						UPDATE ".main::$cnf['db_tables']['users']."
						SET last_login = '".time()."'
						WHERE user_id = '".$staff_member->get_user_id()."' LIMIT 1
					");

					self::$current_admin = $staff_member;
					return true;
				}
				else
				{
					self::$current_admin = false;
					self::kill_staff_session();
					return false;
				}
			}
			else
			{
				self::kill_staff_session();
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public static function get_staff_member($login){
		$staff_member = dao_users::select(' WHERE username = :v_username ',array("v_username"=>$login));
		if(isset($staff_member[0])){
			$staff_member = $staff_member[0];
		}
		else
		{
			$staff_member = array();
		}
		if(count($staff_member) < 1){
			self::increase_login_attempts();
			self::set_action_message(
				'Invalid login. Please try again.'.
				self::get_login_attempt_message()
			);
			
			return false;
		}
		else
		{
			return $staff_member;
		}
	}

	public static function validate_password($staff_member){
		$password = dev::$post['staff_password'].$staff_member->get_salt();
		if(md5($password) != $staff_member->get_password()){
			self::increase_login_attempts();
			self::set_action_message(
				'Invalid login. Please try again.'.
				self::get_login_attempt_message()
			);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public static function get_login_attempt_message(){
		return ' You have used '.
		dev::$session['login_attempts'].
		' of your '.
		main::$cnf['ui_config']['max_failed_login_attempts'].
		' allowed attempts.';
	}
	
	public static function increase_login_attempts(){
		if(
			isset(dev::$session['login_attempts']) && 
			dev::$session['last_login_attempt'] >= 
			time() - (60 * main::$cnf['ui_config']['failed_login_lockout'])
		){
			dev::$session['login_attempts']++;
		} else {
			dev::$session['login_attempts'] = 1;
			dev::$session['last_login_attempt'] = time();
		}
	}
	
	public static function reset_login_attempts(){
		unset(dev::$session['login_attempts']);
		unset(dev::$session['last_login_attempt']);
	}
	
	public static function check_login_attempts(){
		if(
			isset(dev::$session['login_attempts']) && 
			dev::$session['login_attempts'] > main::$cnf['ui_config']['max_failed_login_attempts'] &&
			dev::$session['last_login_attempt'] >= 
			time() - (60 * main::$cnf['ui_config']['failed_login_lockout'])
		){
			
			//Increase Attempts
			//self::increase_login_attempts();
			
			//Get Remaining
			$lockout_seconds = dev::$session['last_login_attempt'] + (main::$cnf['ui_config']['failed_login_lockout'] * 60);
			$remaining_lockout = number_format((($lockout_seconds - time()) / 60),0);
			
			self::set_action_message(
				'You have failed to login more than '.
				main::$cnf['ui_config']['max_failed_login_attempts'].
				' times please try again in '.
				$remaining_lockout.' minutes.'
			);
			
			return false;
			
		} else {
			return true;
		}
	}

	public static function set_staff_session($staff_member){
		dev::$session[main::$cnf['session_name']]['admin'] = array(
			"staff_login"		=>	$staff_member->get_username(),
			"staff_password"	=>	$staff_member->get_password(),
			"staff_email"		=>	$staff_member->get_email()
		);
	}

	public static function kill_staff_session(){
		unset(dev::$session[main::$cnf['session_name']]['admin']);
	}
	
}
?>
