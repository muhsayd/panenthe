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

class ctl_browse_au {

	private $vo_vps;

	public function __construct(){
		
		$this->get_vo_vps();
		$this->set_constants();
		$this->browse_action();
		$this->search();
		$this->pagination();
		$this->get_au();
		$this->show_page();
	}
	
	private function get_vo_vps(){
	
		$vps = func_vps::get_vps_by_id(dev::$get['vps_id']);

		if(isset($vps[0])){
			$this->vo_vps = $vps[0];
		}
		else
		{
			echo "VPS not found.";
			exit;
		}
		
	}
	
	private function set_constants(){
		dev::$tpl->set_constant("hostname",$this->vo_vps->get_hostname());
	}

	private function browse_action(){
	
		if(isset(dev::$get['remove_au'])){
			func_vps_au::remove_au();
		}

		if(isset(dev::$post['browse_action_delete'])){
			func_vps_au::browse_action_delete();
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
		if(!isset(dev::$get['search_words'])){
			$base_url = main::$url.'/index.php?app=vps&sec=browse_au&vps_id='.dev::$get['vps_id'];
			$page_url = main::$url.'/index.php?app=vps&sec=browse_au&vps_id='.dev::$get['vps_id'].'&page_no={page_no}';
		}
		else
		{
			$base_url = main::$url.'/index.php?app=vps&sec=browse_au&vps_id='.dev::$get['vps_id'].'&search_words='.dev::$get['search_words'].'&search_col='.dev::$get['search_col'];
			$page_url = main::$url.'/index.php?app=vps&sec=browse_au&vps_id='.dev::$get['vps_id'].'&search_words='.dev::$get['search_words'].'&search_col='.dev::$get['search_col'].'&page_no={page_no}';
		}
		if(isset(dev::$get['items_per_page'])){
			$base_url .= '&items_per_page='.dev::$get['items_per_page'];
			$page_url .= '&items_per_page='.dev::$get['items_per_page'];
		}
		dev_pagination::set_base_url($base_url);
		dev_pagination::set_page_url($page_url);
		dev_pagination::set_count(func_vps::get_count(dev_search::get_sql(),dev_search::get_sql_value()));

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

	private function get_au(){

		$this->pages = func_vps_au::get_users($this->vo_vps,dev_search::get_sql(),dev_search::get_sql_value(),dev_pagination::get_sql());
		
	}

	private function show_page(){

		vw_au::load_browse_js();
		vw_au::title('Browse VM Assigned Users for VPS ID: '.dev::$get['vps_id']);
		vw_au::browse($this->pages,dev_pagination::get_page_html());
	}

}

?>
