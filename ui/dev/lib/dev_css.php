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

final class dev_css {

	protected $tpl;
	private $css_path;
	private $files;
    private $config;

	public function __construct($config,&$tpl){
		$this->tpl =& $tpl;
        $this->config = $config;
		$this->init();
	}

	private function init(){
		$this->set_css_path($this->config['css']['path']);
	}

	public function set_css_path($path){
		$this->css_path = $path;
	}

	public function get_css_path(){
		return $this->css_path;
	}

	public function add_file($path){
		$this->files[] = $this->get_css_path().'/'.$path;
	}

	public function output(){
		$css_include = '';
		if(is_array($this->files)){
			foreach($this->files AS $path){
				$css_include .= '<link rel="stylesheet" type="text/css" href="'.$path.'" />'."\n";
			}
		}
		$this->tpl->set_constant("script_css",$css_include);
	}
}

?>
