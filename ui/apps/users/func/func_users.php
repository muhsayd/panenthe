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

class func_users {

	public static function get_users($where="",$where_values=array(),$limit="",$staff=false,$online=false,$orphaned=false){

		if($staff){
			$staff = 'is_staff = "1"';
		}
		else
		{
			$staff = 'is_staff = "0"';
		}

		if($online){
			$online = " AND last_refresh > ".(time() - 900)." ";
		}
		else
		{
			$online = '';
		}
		
		if(!empty($where)){
			$where = " WHERE ".$staff." ".$online." AND ".$where;
		}
		else
		{
			$where = " WHERE ".$staff." ".$online;
		}
		
		if($orphaned){
			$where = "WHERE is_staff = 0 ";
			$where_extra = " 
				GROUP BY v.user_id
				HAVING vps_count = 0
				ORDER BY v.user_id DESC
				".$limit;
		} else {
			$where_extra = " ORDER BY user_id DESC ".$limit;
		}
		
		return dao_users::select($where.$where_extra,$where_values,$orphaned);
	}

	public static function get_count($where="",$where_values=array(),$staff=false,$online=false){

		if($staff){
			$staff_where = ' is_staff = 1 ';
		}
		else
		{
			$staff_where = ' is_staff = 0 ';
		}
		
		if($online){
			$online_where = " AND last_refresh > ".(time() - 900)." ";
		}
		else
		{
			$online_where = '';
		}
			
		
		if(!empty($where)){
			$where = " WHERE ".$staff_where.$online_where." AND ".$where;
		}
		else
		{
			$where = " WHERE ".$staff_where.$online_where;
		}
		
		return count(dao_users::select($where,$where_values));
	}

	public static function get_user_by_id($user_id){

		if(
			!main::$is_staff && 
			$user_id != dev::$tpl->get_constant('cur_admin_id')
		){
			main::error_page("You can only modify yourself!");
		}
			
		return dao_users::select(
			"WHERE user_id = :user_id",
			array("user_id"=>$user_id)
		);
		
	}

	public static function save_user(){

		if(isset(dev::$post["user_id"]) && verify_users::insert() ){
		
			if(
				!main::$is_staff && 
				dev::$post['user_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
				main::error_page("You can only modify yourself!");
			}

			$vo_users = new vo_users(dev::$post);
			$vo_users->set_modified(time());
			if(empty(dev::$post["user_id"])){
			
				if(
					main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id') &&
					dev::$post['user_id'] != dev::$tpl->get_constant('cur_admin_id') &&
					dev::$post['is_staff'] == 1
				){
				   main::error_page("Only the root admin can add new staff!");
				}

				$vo_users->set_created(time());
				$salt = substr(md5(time().rand(1,100)),0,12);
				if($vo_users->get_password() == ""){
					$password = substr(md5(time().rand(1,100)),0,12);
					$vo_users->set_password(md5($password.$salt));
					$vo_users->set_salt($salt);
				}
				else
				{
					$vo_users->set_password(md5($vo_users->get_password().$salt));
					$vo_users->set_salt($salt);
				}
				$vo_users->set_created(time());
				dao_users::insert($vo_users->insert_array());
				$vo_users->set_user_id(dev::$db->lastInsertId());
				event_api::add_event('User #'.$vo_users->get_user_id().' "'.$vo_users->get_username().'" was added.');
				if(isset($password)){
					main::set_action_message("User has been added! Password generated is: <span style='color: black;'>".$password."</span>");
				}
				else
				{
					main::set_action_message("User has been added!");
				}
			}
			else
			{
				
				if(
					main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id') &&
					dev::$post['user_id'] != dev::$tpl->get_constant('cur_admin_id') &&
					dev::$post['is_staff'] == 1
				){
				   main::error_page("Only the root admin can update staff!");
				}
				
				if($vo_users->get_password() != ""){
					$salt = substr(md5(time().rand(1,100)),0,12);
					$vo_users->set_password(md5($vo_users->get_password().$salt));
					$vo_users->set_salt($salt);
					$updateArray = "update_array";
				}
				else
				{
					$updateArray = "update_array_no_password";
				}

				dao_users::update(
					$vo_users->$updateArray(),
					" WHERE user_id = :v_user_id ",
					array("v_user_id"=>$vo_users->get_user_id())
				);
				event_api::add_event('User #'.$vo_users->get_user_id().' "'.$vo_users->get_username().'" was updated.');
				main::set_action_message("User has been saved!");
			}
		}
	}

	public static function remove_user(){

		if(isset(dev::$get["remove_user"])){

			$vo_users = func_users::get_user_by_id(dev::$get["remove_user"]);
			$vo_users = $vo_users[0];
			
			if(
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id') &&
				$vo_users->get_is_staff() == 1
			){
			   main::error_page("Only the root admin can remove staff!");
			}
			
			event_api::add_event('User #'.$vo_users->get_user_id().' "'.$vo_users->get_username().'" was removed.');

			dao_users::remove(
				" WHERE user_id = :user_id",
				array("user_id"=>dev::$get["remove_user"])
			);
			
			dev::$db->exec("
				DELETE FROM ".main::$cnf['db_tables']['vps_user_map']."
				WHERE user_id = '".dev::$get['remove_user']."'
			");
			
			main::set_action_message("User has been removed!");
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
		
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_users = func_users::get_user_by_id($delete);
					$vo_users = $vo_users[0];
					
					if(
						main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id') &&
						$vo_users->get_is_staff() == 1
					){
					   main::error_page("Only the root admin can remove staff!");
					}
			
					event_api::add_event('User #'.$vo_users->get_user_id().' "'.$vo_users->get_username().'" was removed.');

					dao_users::remove(
						" WHERE user_id = :user_id",
						array("user_id"=>$delete)
					);
					
					dev::$db->exec("
						DELETE FROM ".main::$cnf['db_tables']['vps_user_map']."
						WHERE user_id = '".$delete."'
					");
					
				}
				main::set_action_message('Users has been removed!');
			}
		}
	}

}

?>
