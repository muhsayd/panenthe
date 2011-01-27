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

class dev {
	
	//Builder Environment
	static $root;
	static $url;
	static $uri;
	static $v_ver = '1.0.1';
	static $v_url = 'http://www.panenthe.com';
	static $ver = array(1,0,1);
	public $dev_config;
	
	//Execution Time
	static $start_time;
	static $end_time;
	
	//Builder Systems
	static $db;
	static $tpl;
	static $sec;
	static $css;
	static $js;
	static $mail;
	
	//Protected Globals
	static $s_post;
	static $s_get;
	static $s_files;
	static $s_session;
	static $s_cookie;
	static $s_request;
	static $s_server;
	static $s_env;

	//Unprotected Globals
	static $post;
	static $get;
	static $files;
	static $session;
	static $cookie;
	static $request;
	static $server;
	static $env;
	
	//Debug Output
	static $flush_debug = false;
	static $dbg_data;
	
	//Void Construct
	public function __construct(){
		self::start_time();
	}
	
	//Start Time
	private static function start_time(){
		self::$start_time = microtime(true);
	}
	
	//End Time
	private static function end_time(){
		self::$end_time = microtime(true);
	}
	
	//Script Exec
	public static function script_exec(){
		self::end_time();
		return number_format((self::$end_time - self::$start_time),5);
	}
	
	//Set Dev Config
	public function dev_config($dev_config){
		
		$this->dev_config = $dev_config;
		
	}
	
	//Init Builder
	public function init(){
		
		$this->paths();
		$this->load();
		$this->db();
		$this->tpl();
		$this->mail();
		$this->sec();
		
	}

	//Set Paths
	private function paths(){
	
		$dev_config = $this->dev_config;
		
		if(!isset($this->dev_config['root'])){
			self::$root = dirname(__FILE__);
		}
		else
		{
			self::$root = $dev_config['root'];
		}
		
		if(!isset($this->dev_config['url'])){
			self::$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
		}
		else
		{
			self::$url = $dev_config['url'];
		}
		
		if(!isset($this->dev_config['uri'])){
			self::$uri = dirname(preg_replace('@'.preg_quote($_SERVER['DOCUMENT_ROOT']).'@si','',preg_replace('@\\\@si','/',__FILE__))).'/';
		}
		else
		{
			self::$uri = $dev_config['uri'];
		}
		
		if(!isset($this->dev_config['skip_include_path']) || !$this->dev_config['skip_include_path']){
			set_include_path(self::$root.'pear');
		}
		
	}
	
	private function load(){
		
		require(self::$root.'/lib/dev_db.php');
		require(self::$root.'/lib/dev_sec.php');
		require(self::$root.'/lib/dev_tpl.php');
        require(self::$root.'/lib/dev_css.php');
        require(self::$root.'/lib/dev_js.php');
		require(self::$root.'/lib/dev_mail.php');
		
	}
	
	private function sec(){
		
		self::$sec 		= new dev_sec();
		self::$post 	= &self::$sec->post;
		self::$get 		= &self::$sec->get;
		self::$files 	= &self::$sec->files;
		self::$session 	= &self::$sec->session;
		self::$cookie	= &self::$sec->cookies;
		self::$request 	= &self::$sec->request;
		self::$server 	= &self::$sec->server;
		self::$env 		= &self::$sec->env;

		self::$s_post		= &self::$sec->s_post;
		self::$s_get 		= &self::$sec->s_get;
		self::$s_files		= &self::$sec->s_files;
		self::$s_session 	= &self::$sec->s_session;
		self::$s_cookie		= &self::$sec->s_cookies;
		self::$s_request 	= &self::$sec->s_request;
		self::$s_server 	= &self::$sec->s_server;
		self::$s_env 		= &self::$sec->s_env;
		
	}
	
	private function db(){
		
		if(isset($this->dev_config['db']['db_enable']) && $this->dev_config['db']['db_enable']){
			self::$db = new dev_db($this->dev_config['db']);
		}
		
	}

	private function mail(){

		if(isset($this->dev_config['mail']['mail_enable']) && $this->dev_config['mail']['mail_enable']){
			self::$mail = new dev_mail($this->dev_config['mail']);
		}

	}
	
	private function tpl(){
		
		if(isset($this->dev_config['tpl']['tpl_enable']) && $this->dev_config['tpl']['tpl_enable']){
			self::$tpl = new dev_tpl($this->dev_config['tpl']);
            self::$css = new dev_css($this->dev_config,self::$tpl);
            self::$js = new dev_js($this->dev_config,self::$tpl);
		}
		
	}
	
	public static function output_r(){
		
		$args = func_get_args();

		$output = '';
		foreach($args AS $var){
			
			$output .= '
			<div class="debug_box"><pre>';
			
			if(is_array($var) || is_object($var)){
				$output .= print_r($var,true);
			}
			else
			{
				$output .= $var;
			}
				
			$output .= '</pre></div>';
		}
		
		if(self::$flush_debug){
			echo $output;
		} else {
			self::$dbg_data .= $output;
		}

	}

	public static function get_performance(){

		$exec_time = self::script_exec();
        if(method_exists(self::$db,"get_query_count")){
            $sql_queries = self::$db->get_query_count();
        }
        else
        {
            $sql_queries = 0;
        }

		return 'Script Execution: '.$exec_time.'secs | SQL Queries: '.$sql_queries.'';

	}
	
}

?>
