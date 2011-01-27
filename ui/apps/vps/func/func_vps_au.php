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

class func_vps_au {

	public static function get_users($vo_vps,$where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " AND ".$where;
		}
		return dao_vps::select_au($vo_vps,$where." ORDER BY user_id ASC ".$limit,$where_values);
		
	}
	
	public static function get_users_select($user_id=false){
		
		if(isset(dev::$s_get['vps_id'])){
			$user_list = dao_users::select('
				WHERE is_staff = 0 AND user_id NOT IN(
					SELECT user_id FROM '.main::$cnf['db_tables']['vps_user_map'].'
					WHERE vps_id = '.dev::$s_get['vps_id'].'
					)
			');
		}
		else
		{
			$user_list = dao_users::select('
				WHERE is_staff = 0
			');
		}
		
		$user_select = '<option value="">--SELECT--</option>';
		foreach($user_list AS $user){
			
			if($user_id !== false && $user_id == $user->get_user_id()){
				$selected = 'selected="selected"';
			}
			else
			{
				$selected = '';
			}
			
			$user_list .= dev::$tpl->parse(
				'vps',
				'user_au_add_row',
				array(
					"selected"		=>	$selected,
					"user_id"		=>	$user->get_user_id(),
					"username"		=>	$user->get_username(),
					"email"			=>	$user->get_email(),
					"first_name"	=>	$user->get_first_name(),
					"last_name"		=>	$user->get_last_name()
				),
				true
			);
			
		}
		
		return $user_list;
	
	}
	
	public static function add_post_au(){
	
		//Get VPS
		$vo_vps = func_vps::get_vps_by_id(dev::$get["vps_id"]);
		$vo_vps = $vo_vps[0];
		
		//Get User
		$user_id = dev::$s_post['user_id'];
		
		self::insert_mapped_user($user_id,$vo_vps->get_vps_id());
		event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" User ID '.$user_id.' was assigned.');
		main::set_action_message("User has been assigned!");
		
	}
	
	public static function insert_mapped_user($user_id,$vps_id){

			dev::$db->exec("
				INSERT INTO ".main::$cnf['db_tables']['vps_user_map']."
				(
					vps_id,
					user_id,
					created,
					modified
				)
				VALUES
				(
					'".$vps_id."',
					'".$user_id."',
					'".time()."',
					'".time()."'
				)
			");
		
	}
	
	public static function remove_au(){

		if(isset(dev::$get["remove_au"])){
			$vo_vps = func_vps::get_vps_by_id(dev::$get["vps_id"]);
			$vo_vps = $vo_vps[0];
			
			event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" User ID '.dev::$get['remove_au'].' was removed.');
			dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['vps_user_map']." WHERE user_id = '".dev::$s_get['remove_au']."' AND vps_id = '".$vo_vps->get_vps_id()."' ");
			main::set_action_message("VM user assignment has been removed!");

		}
		
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_vps = func_vps::get_vps_by_id(dev::$get['vps_id']);
					$vo_vps = $vo_vps[0];
					
					event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" User ID '.$delete.' was removed.');
					dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['vps_user_map']." WHERE user_id = '".$delete."' AND vps_id = '".$vo_vps->get_vps_id()."' ");
				}
				main::set_action_message("VM user assignment has been removed!");
			}
		}
	}

}

?>
