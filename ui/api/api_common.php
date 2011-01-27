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
 
if(!defined("IN_API")){
	exit;
}

ob_start();
session_start();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

define("IS_INCLUDED",true);
define("SCRIPT_ROOT",dirname(dirname(__file__)));

require(SCRIPT_ROOT.'/base.php');
require(SCRIPT_ROOT.'/init/init.php');

dev::$flush_debug = false;

class main {

	static $cnf;
	static $root;
	static $url;
	static $uri;
	static $params;
	static $err;
	static $is_staff = true;
	
	public static function output_from_vo($vo){
	
		$output = '';
		$output_array = $vo->api_array();
		
		foreach($output_array AS $name => $value){
			$inc = isset($inc) ? '&' : '';
			$output .= $inc.urlencode($name).'='.urlencode($value);
		}
		
		return $output;
		
	}
	
	public static function get_params(){
	
		$params = array();
	
		if(!IS_CLI){
			$params = dev::$request;
			unset($params['api_user']);
			unset($params['api_pass']);
		}
		else
		{
			foreach(dev::$server['argv'] AS $key => $value){
				if(preg_match('/^--.+$/i',$value)){
					$value = preg_replace('/^--/i','',$value);
					$params[$value] = dev::$server['argv'][($key + 1)];
				}
				else
				if(preg_match('/^-.+$/i',$value)){
					$value = preg_replace('/^-/i','',$value);
					$params[$value] = true;
				}
			}
		}
	
		return $params;
		
	}
	
}

class api_init extends init {

	protected function process_init(){

		$this->dev = new dev();

		$this->dev_config['tpl']['tpl_enable'] = false;

		//Set Config
		$this->dev->dev_config($this->dev_config);

		//Init Dev
		$this->dev->init();

	}

}

main::$root = dirname(dirname(__FILE__));
main::$cnf = $config;
main::$cnf['ui_config']['server_debug'] = 'false';
main::$cnf['ui_config']['vps_debug'] = 'false';
main::$err = $errors;

new api_init($config);

//Check for CLI Based Call
if(!isset(dev::$server['HTTP_HOST']) || dev::$server['HTTP_HOST'] == ''){
	define("IS_CLI",true);
}
else
{
	define("IS_CLI",false);
}

//Check Username Password
if(!IS_CLI){
	if(!isset(dev::$request['api_user']) || empty(dev::$request['api_user'])){
		die("no_access");
	}
	if(!isset(dev::$request['api_pass']) || empty(dev::$request['api_pass'])){
		die("no_access");
	}
	if(dev::$request['api_user'] != main::$cnf['api_config']['api_user']){
		die("no_access");
	}
	if(dev::$request['api_pass'] != main::$cnf['api_config']['api_pass']){
		die("no_access");
	}
}

main::$params = main::get_params();

?>
