<?php
/**
 * Panenthe
 *
 * Very light PHP Framework
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

function _strip_quotes(&$var) {
	if (is_array($var)) {
		array_walk($var, '_strip_quotes');
	} else {
		$var = stripslashes($var);
	}
}

final class dev_sec {

	//GPC Vars
	private $gpc_vars = array('get','post','cookie');
	
	//Alias Access
	public $post = array();
	public $get = array();
	public $files = array();
	public $session = array();
	public $cookie = array();
	public $request = array();
	public $server = array();
	public $env = array();
	
	//Secure Access
	public $s_post = array();
	public $s_get = array();
	public $s_files = array();
	public $s_session = array();
	public $s_cookie = array();
	public $s_request = array();
	public $s_server = array();
	public $s_env = array();
	
	public function __construct(){
		
		//Init SEC
		$this->init();
		
	}
	
	private function init(){

		if(isset($_POST)) $this->post =& $_POST;
		if(isset($_GET)) $this->get =& $_GET;
		if(isset($_FILES)) $this->files =& $_FILES;
		if(isset($_SESSION)) $this->session =& $_SESSION;
		if(isset($_COOKIE)) $this->cookie =& $_COOKIE;
		if(isset($_REQUEST)) $this->request =& $_REQUEST;
		if(isset($_SERVER)) $this->server =& $_SERVER;
		if(isset($_ENV)) $this->env =& $_ENV;

		$this->init_global('post');
		$this->init_global('get');
		$this->init_global('files');
		$this->init_global('session');
		$this->init_global('cookie');
		$this->init_global('request');
		$this->init_global('server');
		$this->init_global('env');
		
	}
	
	private function init_global($name){
		if(get_magic_quotes_gpc() == '1' && in_array($name,$this->gpc_vars)){
			array_walk($this->$name, '_strip_quotes');
		}
		$secured_name = "s_".$name;
		$this->$secured_name = $this->secure_global($this->$name);
	}
	
	private function secure_global($global){
		
		foreach($global AS $key => $value){
			if(is_array($value)){
				$global[$key] = $this->secure_global($value);
			}
			else
			{
				$global[$key] = mysql_escape_string($value);
			}
		}
		
		return $global;
		
	}
}

?>