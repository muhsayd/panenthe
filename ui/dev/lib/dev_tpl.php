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

final class dev_tpl {
	
	//tpl envrionment
	private $config;
	private $constants;
	private $path;
	private $theme;
	private $body;
	private $templates;
	
	public function __construct($config){
		
		//Save Config
		$this->config = $config;
		
		//Init tpl
		$this->init();
		
	}
	
	private function init(){
		
		if(isset($this->config['tpl_constants'])){
			$this->constants = $this->config['tpl_constants'];
		}
		
		if(isset($this->config['tpl_path'])){
			$this->path = $this->config['tpl_path'];
		}

		if(isset($this->config['tpl_theme'])){
			$this->theme = $this->config['tpl_theme'];
		}
		
		$this->body = '';
		$this->templates = array();

		$this->default_constants();
		
	}

	private function default_constants(){
		$this->add_constant('theme_url',$this->theme);
	}
	
	public function set_constant($name,$value){
		$this->constants[$name] = $value;
	}

	public function set_constants($constants=array()){
		if(count($constants) > 0){
			foreach($constants AS $name => $value){
				$this->constants[$name] = $value;
			}
		}
	}

	public function add_constant($name,$value){
		$this->set_constant($name,$value);
	}
	
	public function get_constant($name){
		if(isset($this->constants[$name])){
			return $this->constants[$name];
		}
		else
		{
			return false;
		}
	}
	
	public function reset_body(){
		$this->body = '';
	}
	
	public function parse($file,$section,$tags=array(),$return=false){
		
		$this->load_file($file);
		
		if(isset($this->templates[$file][$section])){
			
			$data = $this->templates[$file][$section];
		
			//Replace Tags
			if(is_array($tags) && count($tags) > 0){
				foreach($tags AS $tag => $value){
					
					$tag = (string) strval($tag);
					$value = (string) strval($value);
					
					if($tag != ''){
						
						$rule = '@'.preg_quote('{'.$tag.'}','@').'@si';
						
						//Very Messy Problem with Backreference parsing
						//Thanks to http://www.procata.com/blog/archives/2005/11/13/two-preg_replace-escaping-gotchas/
						$value = preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', $value);
						
						$data = preg_replace($rule,$value,$data);
						
					}
					
				}
			}
			
			if($return){
				return $data;
			}
			else
			{
				$this->body .= $data;
			}
			
		}
		
		return false;
		
	}
	
	private function load_file($file){
		
		if(!isset($this->templates[$file])){
			include($this->path.'/'.$file.'.tpl.php');
			if(isset($templates)){
				$this->templates[$file] = $templates;
			}
			else
			{
				$this->templates[$file] = array();
			}
			unset($templates);
		}
		
	}
	
	private function parse_constants(){

		if(is_array($this->constants) && count($this->constants) > 0){
			foreach($this->constants AS $tag => $value){

				$tag = (string) strval($tag);
				$value = (string) strval($value);

				if($tag != ''){

					$rule = '@'.preg_quote('{'.$tag.'}','@').'@si';

					//Very Messy Problem with Backreference parsing
					//Thanks to http://www.procata.com/blog/archives/2005/11/13/two-preg_replace-escaping-gotchas/
					$value = preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', $value);

					$this->body = preg_replace($rule,$value,$this->body);

				}

			}
		}
		
	}
	
	public function output(){
		
		$this->parse_constants();
		$body = $this->body;
		$this->reset_body();
		return trim($body);
	}
}

?>