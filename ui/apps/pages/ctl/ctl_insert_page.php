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

class ctl_insert_page{

	private $fields;
	private $page_content;

	public function __construct(){
		$this->post();
		$this->fckeditor();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){
		if(isset(dev::$post['page_name'])){
			func_pages::save_page();
		}
	}

	private function fckeditor(){
		require(main::$root.'/js/fckeditor/fckeditor.php');
		$fck = new FCKeditor('page_content');
		$fck->BasePath = main::$uri.'/js/fckeditor/';
		$fck->Height = '300';
		if(isset(dev::$post['page_content'])){
			$fck->Value = dev::$post['page_content'];
		}
		$this->page_content = $fck->CreateHTML();
	}

	private function check_fields(){
		$this->fields = array(
			'page_id'		=>	"",
			'page_name'		=>	"",
			'page_content'	=>	""
		);
		if(isset(dev::$post['page_name'])){
			$this->fields = dev::$post;
		}
		$this->fields['page_content'] = $this->page_content;
	}

	private function show_page(){
		vw_pages::title('Insert Page');
		vw_pages::form($this->fields);
	}
}

?>
