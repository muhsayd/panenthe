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

class func_events {

	public static function get_events($where="",$where_values=array(),$limit="",$ack=false){

		if($ack){
			$ack_value = '1';
		}
		else
		{
			$ack_value = '0';
		}

		if(!empty($where)){
			$where = " WHERE ".$where." AND is_acknowledged = ".$ack_value." ";
		}
		else
		{
			$where = " WHERE is_acknowledged = ".$ack_value." ";
		}
		return dao_events::select($where." ORDER BY event_id DESC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array(),$ack=false){

		if($ack){
			$ack_value = '1';
		}
		else
		{
			$ack_value = '0';
		}
		
		if(!empty($where)){
			$where = " WHERE ".$where." AND is_acknowledged = ".$ack_value." ";
		}
		else
		{
			$where = " WHERE is_acknowledged = ".$ack_value." ";
		}
		return count(dao_events::select($where,$where_values));
	}

	public static function get_event_by_id($event_id){

		return dao_events::select(
			"WHERE event_id = :event_id",
			array("event_id"=>$event_id)
		);
	}

	public static function save_event(){

		if(isset(dev::$post["event_id"]) && verify_events::insert() ){
			$vo_events = new vo_events(dev::$post);
			$vo_events->set_is_acknowledged(0);
			if(empty(dev::$post["event_id"])){
				$vo_events->set_time(time());
				$vo_events->set_created(time());
				$vo_events->set_modified(time());
				dao_events::insert($vo_events->insert_array());
				$vo_events->set_event_id(dev::$db->lastInsertId());
				//event_api::add_event('Event #'.$vo_events->get_event_id().' "'.$vo_events->get_message().'" was added.');
				main::set_action_message("Event has been added!");
			}
			else
			{
				$vo_events->set_modified(time());
				dao_events::update(
					$vo_events->update_array(),
					" WHERE event_id = :v_event_id ",
					array("v_event_id"=>$vo_events->get_event_id())
				);
				main::set_action_message("Event has been saved!");
			}
		}
	}

	public static function acknowledge_event(){

		if(isset(dev::$get["acknowledge_event"])){
			$events = func_events::get_event_by_id(dev::$get["acknowledge_event"]);
			if(isset($events[0])){
				$event = new vo_events($events[0]);
				$event->set_is_acknowledged(1);
				dao_events::update(
					$event->update_array(),
					" WHERE event_id = :event_id",
					array("event_id"=>dev::$get["acknowledge_event"])
				);
				main::set_action_message("Event has been acknowledged!");
			}
		}
	}

	public static function browse_action_acknowledge(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$events = func_events::get_event_by_id($delete);
					if(isset($events[0])){
						$event = new vo_events($events[0]);
						$event->set_is_acknowledged(1);
						dao_events::update(
							$event->update_array(),
							" WHERE event_id = :event_id",
							array("event_id"=>$delete)
						);
					}
				}
				main::set_action_message("Events have been acknowledged!");
			}
		}
	}

	public static function remove_event(){

		if(isset(dev::$get["remove_event"])){
			dao_events::remove(
				" WHERE event_id = :event_id",
				array("event_id"=>dev::$get["remove_event"])
			);
			main::set_action_message("Event has been removed!");
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					dao_events::remove(
						" WHERE event_id = :event_id",
						array("event_id"=>$delete)
					);
				}
				main::set_action_message('Events have been removed!');
			}
		}
	}

}

?>
