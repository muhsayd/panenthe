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

class ctl_update_page{

	private $page;
	private $fields;
	private $page_content;

	public function __construct(){
		$this->post();
		$this->get_page();
		$this->fckeditor();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){
		if(isset(dev::$post['page_name'])){
			func_pages::save_page();
		}
	}

	private function get_page(){
		if(isset(dev::$get['page_id'])){
			$page = func_pages::get_page_by_id(dev::$get['page_id']);
			$this->page = $page[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=pages&sec=insert");
		}
	}

	private function fckeditor(){
		require(main::$root.'/js/fckeditor/fckeditor.php');
		$fck = new FCKeditor('page_content');
		$fck->BasePath = main::$uri.'/js/fckeditor/';
		$fck->Height = '300';
		$fck->Value = $this->page->get_page_content();
		$this->page_content = $fck->CreateHTML();
	}

	private function check_fields(){
		$this->fields['page_id'] = $this->page->get_page_id();
		$this->fields['page_name'] = $this->page->get_page_name();
		$this->fields['page_content'] = $this->page_content;
	}

	private function show_page(){
		vw_pages::title('Update Page');
		vw_pages::form($this->fields);
	}
}

?>