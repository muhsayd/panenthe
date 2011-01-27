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

class func_settings {
	
	static $message = '';
	static $continue = true;
	static $conf = '';
	static $orig_conf = '';
	
	public static function get_message(){
		return self::$message;
	}
	
	public static function set_message($message){
		self::$message = $message;
	}
	
	public static function save_settings(){

		//Make Sure we have data		if(!is_array(dev::$post) || count(dev::$post) == 0){
			self::set_message("Error: The input is invalid.");
			return;
		}
		
		//Make sure its the right data
		if(!isset(dev::$post['form_action']) || dev::$post['form_action'] != 'save_settings'){
			self::set_message("Error: The input is not meant for this system.");
			return;
		}
		
		//Open Config File
		self::open_file();
		
		//Main Settings
		self::save_setting('main','license_user');
		if(dev::$post['license_pass'] != ''){
			self::save_setting('main','license_pass');
		}
		
		//UI Settings
		self::save_setting('ui_config','site_name');
		self::save_setting('ui_config','logo_url');
		self::save_setting('ui_config','head_title');
		self::save_setting('ui_config','url');
		self::save_setting('ui_config','ssl_url');
		self::save_setting('ui_config','login_url');
		self::save_setting('ui_config','ssl_login_url');
		
		if(isset(dev::$post['forgot_password'])){
			$forgot_password = 'true';
		} else {
			$forgot_password = 'false';
		}
		self::save_setting('ui_config','forgot_password',$forgot_password);
		
		self::save_setting('ui_config','max_failed_login_attempts');
		self::save_setting('ui_config','failed_login_lockout');
		
		//API Settings
		self::save_setting('api_config','api_user');
		if(dev::$post['api_pass'] != ''){
			self::save_setting('api_config','api_pass');
		}
		
		//Mail Settings
		self::save_setting('mail','default_from');
		self::save_setting('mail','default_replyto');
		
		if(isset(dev::$post['smtp_enable'])){
			$smtp_enable = 'true';
		} else {
			$smtp_enable = 'false';
		}
		self::save_setting('mail','smtp_enable',$smtp_enable);
		
		self::save_setting('mail','smtp_host');
		self::save_setting('mail','smtp_port');
		
		if(isset(dev::$post['smtp_auth'])){
			$smtp_auth = 'true';
		} else {
			$smtp_auth = 'false';
		}
		self::save_setting('mail','smtp_auth',$smtp_auth);
		
		self::save_setting('mail','smtp_user');
		
		if(dev::$post['smtp_pass'] != ''){
			self::save_setting('mail','smtp_pass');
		}
		
		if(self::$continue){
			
			//Write File
			self::write_file();
			
			//Success
			self::set_message("Settings have been saved successfully!");
			
		}
	
	}
	
	public static function open_file(){
		self::$orig_conf = file_get_contents(main::$cnf['main']['root_dir'].'/shared/etc/panenthe.conf');
		self::setting_sections();
	}
	
	public static function write_file(){
		
		$final = '';
		foreach(self::$conf AS $sec => $cn){
			$final .= '['.$sec.']'."\n";
			$final .= implode("\n",$cn);
			$final .= "\n\n";
		}
		
		file_put_contents(
			main::$cnf['main']['root_dir'].'/shared/etc/panenthe.conf',
			$final
		);
	
	}
	
	public static function setting_sections(){
	
		//Explode By Sections
		preg_match_all('/\[(.+?)\]/i',self::$orig_conf,$sections);
		$section_cn = preg_split('/\[(.+?)\]/i',self::$orig_conf);
		array_shift($section_cn);
		
		//Save Sections and raw content
		foreach($sections[1] AS $sid => $section){
		
			//Explode Lines and remove empty and comments
			$lines = explode("\n",$section_cn[$sid]);
			foreach($lines AS $key => $line){
				$line = trim($line);
				if(trim($line) == '' || $line{0} == '#'){
					unset($lines[$key]);
				}
			}
			
			//Save to Config
			self::$conf[$section] = $lines;
			unset($lines);
			
		}
		
	}

	public static function save_setting($sec,$item,$value=false){
	
		if(self::$continue){
			
			//Try to get value if none sent.
			if($value === false && isset(dev::$post[$item])){
				$value = dev::$post[$item];
			}
			
			//Still no value? fail and quit
			if($value === false){
				self::set_message("Error: Value not found for ".$item.".");
				self::$continue = false;
				return;
			}
			
			//Check for Section
			if(!isset(self::$conf[$sec])){
				self::$conf[$sec] = array();
			}
			
			$the_lines =& self::$conf[$sec];
			
			//Check the Lines for Ours
			foreach($the_lines AS $ln => $line){
				
				$parts = explode('=',$line);
				$name = trim($parts[0]);
				
				if($name == $item){
					unset($the_lines[$ln]);
					break;
				}
				
			}
			
			//No Item? Add
			if($value != ''){
				$the_lines[] = $item.' = "'.$value.'"';
			} else {
				$the_lines[] = $item.' =';
			}
			
		}
	}
	
}

?>
