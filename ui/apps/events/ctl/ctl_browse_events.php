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

class ctl_browse_events {

	private $ack;

	public function __construct($ack=false){
		$this->ack = $ack;
		$this->browse_action();
		$this->search();
		$this->pagination();
		$this->get_events();
		$this->show_page();
	}

	private function browse_action(){
		if(isset(dev::$post['browse_action_delete'])){
			func_events::browse_action_delete();
		}
		if(isset(dev::$post['browse_action_acknowledge'])){
			func_events::browse_action_acknowledge();
		}
	}

	private function search(){

		if(isset(dev::$get['search_words'])){
			dev_search::set_column(dev::$get['search_col']);
			dev_search::set_keywords(dev::$get['search_words']);
			dev_search::generate();
		}
	}

	private function pagination(){

		if(isset(dev::$get['page_no'])){
			$page = dev::$get['page_no'];
		}
		else
		{
			$page = '1';
		}

		dev_pagination::set_page($page);
		if($this->ack){
			$browse_sec = 'browse_ack';
		}
		else
		{
			$browse_sec = 'browse_events';
		}
		if(!isset(dev::$get['search_words'])){
			$base_url = main::$url.'/index.php?app=events&sec='.$browse_sec;
			$page_url = main::$url.'/index.php?app=events&sec='.$browse_sec.'&page_no={page_no}';
		}
		else
		{
			$base_url = main::$url.'/index.php?app=events&sec='.$browse_sec.'&search_words='.dev::$get['search_words'].'&search_col='.dev::$get['search_col'];
			$page_url = main::$url.'/index.php?app=events&sec='.$browse_sec.'&search_words='.dev::$get['search_words'].'&search_col='.dev::$get['search_col'].'&page_no={page_no}';
		}
		if(isset(dev::$get['items_per_page'])){
			$base_url .= '&items_per_page='.dev::$get['items_per_page'];
			$page_url .= '&items_per_page='.dev::$get['items_per_page'];
		}
		dev_pagination::set_base_url($base_url);
		dev_pagination::set_page_url($page_url);
		dev_pagination::set_count(func_events::get_count(dev_search::get_sql(),dev_search::get_sql_value(),$this->ack));

		if(isset(dev::$get['items_per_page']) && dev::$get['items_per_page'] != '0'){
			$limit = dev::$get['items_per_page'];
		}
		else
		if(isset(dev::$get['items_per_page']) && dev::$get['items_per_page'] == '0'){
			$limit = dev_pagination::get_count();
		}
		else
		{
			$limit = main::$cnf['items_per_page'];
		}
		dev_pagination::set_limit($limit);
		dev_pagination::generate();

	}

	private function get_events(){

		$this->pages = func_events::get_events(dev_search::get_sql(),dev_search::get_sql_value(),dev_pagination::get_sql(),$this->ack);
	}

	private function show_page(){
		
		if($this->ack){
			$title = 'Browse Acknowledged Events';
		}
		else
		{
			$title = 'Browse Events';
		}

		vw_events::load_browse_js();
		vw_events::title($title);
		vw_events::browse($this->pages,dev_pagination::get_page_html(),$this->ack);
	}

}

?>