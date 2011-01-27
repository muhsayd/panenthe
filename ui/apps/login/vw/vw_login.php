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

class vw_login {

	public static function load_js(){
		dev::$js->add_library('js/mootools-1.2.1-core.js');
		dev::$js->add_library('js/mootools-1.2-more.js');
		dev::$js->add_library('js/loading_link.js');
		dev::$js->add_library('js/loginFocus.js');
	}

	public static function load_css(){
		dev::$css->add_file('login.css');
	}

    public static function login($action_message){
		if(!empty($action_message)){
			$action_message = dev::$tpl->parse(
				'login',
				'message',
				array("message"=>$action_message),
				true
			);
		}
		
		if(main::$cnf['ui_config']['forgot_password'] == 'true'){
			$fp_link = dev::$tpl->parse(
				'login',
				'fp_link',
				array(),
				true
			);
		} else {
			$fp_link = '';
		}
		
        dev::$tpl->parse(
            'login',
            'login',
			array(
				"action_message"	=>	$action_message,
				"fp_link"			=>	$fp_link
			)
        );
    }
    
    public static function forgot_password($action_message){
		if(!empty($action_message)){
			$action_message = dev::$tpl->parse(
				'login',
				'message',
				array("message"=>$action_message),
				true
			);
		}
        dev::$tpl->parse(
            'login',
            'forgot_password',
			array(
				"action_message"	=>	$action_message
			)
        );
    }
    
    public static function forgot_password_confirmation(){

        dev::$tpl->parse(
            'login',
            'forgot_password_confirmation'
        );
        
    }
    
}

?>
