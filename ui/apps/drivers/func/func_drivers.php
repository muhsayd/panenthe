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

class func_drivers {

	public static function get_drivers($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_drivers::select($where." ORDER BY driver_id DESC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array()){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_drivers::select($where,$where_values));
	}

	public static function get_driver_by_id($driver_id){

		return dao_drivers::select(
			"WHERE driver_id = :driver_id",
			array("driver_id"=>$driver_id)
		);
	}

	public static function save_driver(){

		if(isset(dev::$post["driver_id"]) && verify_drivers::insert()){
			$vo_drivers = new vo_drivers(dev::$post);
			if(empty(dev::$post["driver_id"])){
				dao_drivers::insert($vo_drivers->insert_array());
				$vo_drivers->set_driver_id(dev::$db->lastInsertId());
				event_api::add_event('Driver #'.$vo_drivers->get_driver_id().' "'.$vo_drivers->get_name().'" was added.');
				main::set_action_message("Driver has been added!");
			}
			else
			{
				dao_drivers::update(
					$vo_drivers->update_array(),
					" WHERE driver_id = :v_driver_id ",
					array("v_driver_id"=>$vo_drivers->get_driver_id())
				);
				event_api::add_event('Driver #'.$vo_drivers->get_driver_id().' "'.$vo_drivers->get_name().'" was updated.');
				main::set_action_message("Driver has been saved!");
			}
		}
	}

	public static function remove_driver(){

		if(isset(dev::$get["remove_driver"])){
			$vo_drivers = func_drivers::get_driver_by_id(dev::$get['remove_driver']);
			$vo_drivers = $vo_drivers[0];
			event_api::add_event('Driver #'.$vo_drivers->get_driver_id().' "'.$vo_drivers->get_name().'" was removed.');
			dao_drivers::remove(
				" WHERE driver_id = :driver_id",
				array("driver_id"=>dev::$get["remove_driver"])
			);

			main::set_action_message("Driver has been removed!");
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_drivers = func_drivers::get_driver_by_id($delete);
					$vo_drivers = $vo_drivers[0];
					event_api::add_event('Driver #'.$vo_drivers->get_driver_id().' "'.$vo_drivers->get_name().'" was removed.');
					dao_drivers::remove(
						" WHERE driver_id = :driver_id",
						array("driver_id"=>$delete)
					);
				}
				main::set_action_message('Drivers have been removed!');
			}
		}
	}

}

?>
