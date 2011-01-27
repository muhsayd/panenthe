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

class func_vps_user {

	public static function add_vps_user($userArray,$vo_vps,$return_vo_user=false){

		$password = false;
		
		if($userArray['user_add_type'] == 'add_user'){
			//Save User
			$userArray['is_staff'] = '0';
			$vo_users = new vo_users($userArray);
			$return_password = $vo_users->get_password();
			$salt = substr(md5(time().rand(1,100)),0,12);
			if($vo_users->get_password() == ""){
				$password = substr(md5(time().rand(1,100)),0,12);
				$return_password = $password;
				$vo_users->set_password(md5($password.$salt));
				$vo_users->set_salt($salt);
			}
			else
			{
				$vo_users->set_password(md5($vo_users->get_password().$salt));
				$vo_users->set_salt($salt);
			}
			$vo_users->set_created(time());
			$vo_users->set_modified(time());
			dao_users::insert($vo_users->insert_array());
			$vo_users->set_user_id(dev::$db->lastInsertId());
			
			$user_id = $vo_users->get_user_id();
			
			
			//Save User Event
			event_api::add_event('Users #'.$vo_users->get_user_id().' "'.$vo_users->get_username().'" was added.');
		
		}
		else
		{
			$user_id = $userArray['user_id'];
			$return_password = false;
		}

		//Map User to VPS
		dev::$db->exec("
			INSERT INTO ".main::$cnf['db_tables']['vps_user_map']."
			(
				vps_id,
				user_id,
				created,
				modified
			)
			VALUE
			(
				'".$vo_vps->get_vps_id()."',
				'".$user_id."',
				'".time()."',
				'".time()."'
			)
		");

		if($return_vo_user){
			$rows = func_users::get_user_by_id($user_id);
			if(isset($rows[0])){
				$vo_user = $rows[0];
			}
			else
			{
				$vo_user = false;
			}
			return array($return_password,$vo_user);
		}
		else
		{
			return $return_password;
		}
		
	}
}

?>
