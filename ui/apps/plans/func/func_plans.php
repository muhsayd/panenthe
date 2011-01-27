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

class func_plans {

	public static function get_plans($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_plans::select($where." ORDER BY plan_id DESC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array()){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_plans::select($where,$where_values));
	}

	public static function get_plan_by_id($plan_id){

		return dao_plans::select(
			"WHERE plan_id = :plan_id",
			array("plan_id"=>$plan_id)
		);
	}

	public static function save_plan(){

		if(isset(dev::$post["plan_id"]) && verify_plans::insert() ){
			$vo_plans = new vo_plans(dev::$post);
			$vo_plans->set_modified(time());
			if(empty(dev::$post["plan_id"])){
				$vo_plans->set_created(time());
				dao_plans::insert($vo_plans->insert_array());
				$vo_plans->set_plan_id(dev::$db->lastInsertId());
				event_api::add_event('Resource Plan #'.$vo_plans->get_plan_id().' "'.$vo_plans->get_name().'" was added.');
				main::set_action_message("Plan has been added!");
			}
			else
			{
				dao_plans::update(
					$vo_plans->update_array(),
					" WHERE plan_id = :v_plan_id ",
					array("v_plan_id"=>$vo_plans->get_plan_id())
				);
				event_api::add_event('Resource Plan #'.$vo_plans->get_plan_id().' "'.$vo_plans->get_name().'" was updated.');
				main::set_action_message("Plan has been saved!");
			}
		}
	}

	public static function remove_plan(){

		if(isset(dev::$get["remove_plan"])){
			$vo_plans = func_plans::get_plan_by_id(dev::$get['remove_plan']);
			$vo_plans = $vo_plans[0];
			event_api::add_event('Resource Plan #'.$vo_plans->get_plan_id().' "'.$vo_plans->get_name().'" was removed.');
			dao_plans::remove(
				" WHERE plan_id = :plan_id",
				array("plan_id"=>dev::$get["remove_plan"])
			);
			main::set_action_message("Plan has been removed!");
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_plans = func_plans::get_plan_by_id($delete);
					$vo_plans = $vo_plans[0];
					event_api::add_event('Resource Plan #'.$vo_plans->get_plan_id().' "'.$vo_plans->get_name().'" was removed.');
					dao_plans::remove(
						" WHERE plan_id = :plan_id",
						array("plan_id"=>$delete)
					);
				}
				main::set_action_message('Plans has been removed!');
			}
		}
	}

}

?>
