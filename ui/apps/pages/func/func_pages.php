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

class func_pages {

	public static function get_pages($where='',$where_values=array(),$limit=''){
		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_pages::select($where." ORDER BY page_name ASC ".$limit,$where_values);
	}

	public static function get_count($where='',$where_values=array()){
		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_pages::select($where,$where_values));
	}

	public static function get_page_by_id($page_id){
		return dao_pages::select(
			"WHERE page_id = :page_id",
			array("page_id"=>$page_id)
		);
	}

	public static function save_page(){
		if(isset(dev::$post['page_name'])){
			$vo_pages = new vo_pages(dev::$post);
			if(empty(dev::$post['page_id'])){
				dao_pages::insert($vo_pages->insert_array());
				$vo_pages->set_page_id(dev::$db->lastInsertId());
				event_api::add_event('Page #'.$vo_pages->get_page_id().' "'.$vo_pages->get_name().'" was added.');
				main::set_action_message('Page has been added!');
			}
			else
			{
				dao_pages::update(
					$vo_pages->update_array(),
					" WHERE page_id = :page_id ",
					array("page_id"=>$vo_pages->get_page_id())
				);
				event_api::add_event('Page #'.$vo_pages->get_page_id().' "'.$vo_pages->get_name().'" was updated.');
				main::set_action_message('Page has been saved!');
			}
		}
	}

	public static function remove_page(){
		if(isset(dev::$get['remove_page'])){
			$vo_pages = func_pages::get_page_by_id(dev::$get['remove_page']);
			$vo_pages = $vo_pages[0];
			event_api::add_event('Page #'.$vo_pages->get_page_id().' "'.$vo_pages->get_name().'" was added.');
			dao_pages::remove(
				" WHERE page_id = :page_id",
				array("page_id"=>dev::$get['remove_page'])
			);
			
			main::set_action_message('Page has been removed!');
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_pages = func_pages::get_page_by_id($delete);
					$vo_pages = $vo_pages[0];
					event_api::add_event('Page #'.$vo_pages->get_page_id().' "'.$vo_pages->get_name().'" was added.');
					dao_pages::remove(
						" WHERE page_id = :page_id",
						array("page_id"=>$delete)
					);
				}
				main::set_action_message('Page has been removed!');
			}
		}
	}
}

?>
