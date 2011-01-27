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

class ctl_update_settings {

	private $config;
	private $fields;
	private $show_form = true;

	public function __construct(){

		$this->post();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['form_action']) && dev::$post['form_action'] == 'save_settings'){
			func_settings::save_settings();
			vw_settings::confirmation();
			$this->show_form = false;
		}
		
	}

	private function check_fields(){
	
		//Get Config
		$this->config = main::$cnf;
		
		//Setup Fields
		$this->fields = array();
		
		//Main
		$this->fields['license_user'] = $this->config['main']['license_user'];
		
		//UI Config
		$this->fields['site_name'] = $this->config['ui_config']['site_name'];
		$this->fields['logo_url'] = $this->config['ui_config']['logo_url'];
		$this->fields['head_title'] = $this->config['ui_config']['head_title'];
		$this->fields['url'] = $this->config['ui_config']['url'];
		$this->fields['ssl_url'] = $this->config['ui_config']['ssl_url'];
		$this->fields['login_url'] = $this->config['ui_config']['login_url'];
		$this->fields['ssl_login_url'] = $this->config['ui_config']['ssl_login_url'];
		
		if($this->config['ui_config']['forgot_password'] == 'true'){
			$this->fields['forgot_password'] = 'checked="checked"';
		} else {
			$this->fields['forgot_password'] = '';
		}
		
		$this->fields['max_failed_login_attempts'] = $this->config['ui_config']['max_failed_login_attempts'];
		$this->fields['failed_login_lockout'] = $this->config['ui_config']['failed_login_lockout'];
		
		//API Config
		$this->fields['api_user'] = $this->config['api_config']['api_user'];
		
		//Mail Config
		$this->fields['default_from'] = $this->config['mail']['default_from'];
		$this->fields['default_replyto'] = $this->config['mail']['default_replyto'];
		
		if($this->config['mail']['smtp_enable'] == 'true'){
			$this->fields['smtp_enable'] = 'checked="checked"';
		} else {
			$this->fields['smtp_enable'] = '';
		}
		
		$this->fields['smtp_host'] = $this->config['mail']['smtp_host'];
		$this->fields['smtp_port'] = $this->config['mail']['smtp_port'];
		
		if($this->config['mail']['smtp_auth'] == 'true'){
			$this->fields['smtp_auth'] = 'checked="checked"';
		} else{
			$this->fields['smtp_auth'] = '';
		}
		
		$this->fields['smtp_user'] = $this->config['mail']['smtp_user'];
		$this->fields['smtp_pass'] = '';
		
	}

	private function show_page(){
		if($this->show_form){
			vw_settings::title('Change Settings');
			vw_settings::form($this->fields);
		}
	}

}

?>
