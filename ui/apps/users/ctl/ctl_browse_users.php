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

class ctl_browse_users {

	private $staff;
	private $online;
	private $orphaned;

	public function __construct($staff=false,$online=false,$orphaned=false){
		main::check_permissions('browse_users');
		$this->staff = $staff;
		$this->online = $online;
		$this->orphaned = $orphaned;
		$this->browse_action();
		$this->search();
		$this->pagination();
		$this->get_users();
		$this->show_page();
	}

	private function browse_action(){

		if(isset(dev::$post['browse_action_delete'])){
			func_users::browse_action_delete();
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

		if($this->staff){
		    $sec = 'browse_staff';
		}
		else
		{
		    $sec = 'browse_clients';
		}

		if($this->staff && $this->online){
			$sec = 'browse_staff_online';
		}
		else
		if($this->online){
			$sec = 'browse_clients_online';
		}
		
		if($this->orphaned){
			$sec = 'browse_orphaned_clients';
		}

		dev_pagination::set_page($page);
		if(!isset(dev::$get['search_words'])){
			$base_url = main::$url.'/index.php?app=users&sec='.$sec.'';
			$page_url = main::$url.'/index.php?app=users&sec='.$sec.'&page_no={page_no}';
		}
		else
		{
			$base_url = main::$url.'/index.php?app=users&sec='.$sec.'&search_words='.dev::$get['search_words'].'&search_col='.dev::$get['search_col'];
			$page_url = main::$url.'/index.php?app=users&sec='.$sec.'&search_words='.dev::$get['search_words'].'&search_col='.dev::$get['search_col'].'&page_no={page_no}';
		}
		if(isset(dev::$get['items_per_page'])){
			$base_url .= '&items_per_page='.dev::$get['items_per_page'];
			$page_url .= '&items_per_page='.dev::$get['items_per_page'];
		}
		dev_pagination::set_base_url($base_url);
		dev_pagination::set_page_url($page_url);
		dev_pagination::set_count(func_users::get_count(dev_search::get_sql(),dev_search::get_sql_value(),$this->staff,$this->online));

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

	private function get_users(){

		$this->pages = func_users::get_users(dev_search::get_sql(),dev_search::get_sql_value(),dev_pagination::get_sql(),$this->staff,$this->online,$this->orphaned);
	}

	private function show_page(){

		if($this->staff){
		    $sec = 'browse_staff';
		}
		else
		{
		    $sec = 'browse_clients';
		}

		if($this->staff && $this->online){
			$sec = 'browse_staff_online';
		}
		else
		if($this->online){
			$sec = 'browse_clients_online';
		}
		
		if($this->orphaned){
			$sec = 'browse_orphaned_clients';
		}

		vw_users::load_browse_js();

		if($this->staff && $this->online){
			vw_users::title('Browse Staff Online');
		}
		else
		if($this->online){
			vw_users::title('Browse Clients Online');
		}
		else
		if($this->staff){
			vw_users::title('Browse Staff');
		}
		else
		{
			vw_users::title('Browse Clients');
		}
		
		if($this->orphaned){
			vw_users::title('Browse Orphaned Clients');
		}
		
		vw_users::browse($this->pages,dev_pagination::get_page_html(),$sec);
	}

}

?>
