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

final class dev_js {

	protected $tpl;
    private $config;
	private $js_path;
	private $libraries;

	public function __construct($config,&$tpl){
		$this->tpl =& $tpl;
        $this->config = $config;
		$this->init();
	}

	private function init(){
		$this->set_js_path($this->config['js']['path']);
	}

	public function set_js_path($path){
		$this->js_path = $path;
	}

	public function get_js_path(){
		return $this->js_path;
	}

	public function add_library($path){
		$this->libraries[] = $this->get_js_path().'/'.$path;
	}

	public function output(){
		$js_include = '';
		if(is_array($this->libraries)){
			foreach($this->libraries AS $path){
				$js_include .= '<script type="text/javascript" src="'.$path.'"></script>'."\n";
			}
		}
		$this->tpl->set_constant("script_javascript",$js_include);
	}
}

?>
