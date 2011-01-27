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

class dao_vps {

	public static function select($where_statement,$where_values=array()){

		$result = array();

		$query = dev::$db->prepare("
			SELECT *
			FROM ".main::$cnf['db_tables']['vps']."
			".$where_statement."
		");

		$chk = $query->execute($where_values);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		foreach($query->fetchAll() AS $fetch){
			$result[] = new vo_vps($fetch);
		}

		return $result;

	}

	public static function select_with_ost($where_statement,$where_values=array()){

		$result = array();
		
		$client_where = '';
		if(!main::$is_staff){
			if(preg_match('/where/i',$where_statement)){
				$where_statement = ' AND '.$where_statement;
			}
			$where_statement = str_ireplace("where","",$where_statement);
			
			$client_where .= "
				WHERE v.vps_id IN (
					SELECT vps_id FROM ".main::$cnf['db_tables']['vps_user_map']."
					WHERE user_id = '".dev::$tpl->get_constant('cur_admin_id')."'
				)
			";
		}

		$query = dev::$db->prepare("
			SELECT v.*, o.name AS ost, o.ost_id AS ost_id
			FROM ".main::$cnf['db_tables']['vps']." AS v
			LEFT JOIN ".main::$cnf['db_tables']['ost']." AS o
			ON o.ost_id = v.ost
			".$client_where." ".$where_statement." 
		");

		$chk = $query->execute($where_values);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		$i = 0;
		foreach($query->fetchAll() AS $fetch){
			$result[$i] = new vo_vps($fetch);
			$result[$i]->extra['ost_id'] = $fetch['ost_id'];
			$i++;
		}

		return $result;

	}
	
	public static function select_ips($vo_vps,$where_statement,$where_values=array()){

		$result = array();

		$query = dev::$db->prepare("
			SELECT *
			FROM ".main::$cnf['db_tables']['ip_map']."
			WHERE vps_id = '".$vo_vps->get_vps_id()."' ".$where_statement."
		");

		$chk = $query->execute($where_values);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		foreach($query->fetchAll() AS $fetch){
			$result[] = array(
				"ip_id"		=>	$fetch['ip_id'],
				"vps_id"	=>	$vo_vps->get_vps_id(),
				"real_id"	=>	$vo_vps->get_real_id(),
				"hostname"	=>	$vo_vps->get_hostname(),
				"ip_addr"	=>	$fetch['ip_addr']
			);
		}

		return $result;

	}
	
	public static function select_au($vo_vps,$where_statement,$where_values=array()){

		$result = array();

		$query = dev::$db->prepare("
			SELECT u.*, u.user_id AS user_id, m.vps_id AS vps_id
			FROM ".main::$cnf['db_tables']['vps_user_map']." AS m
			LEFT JOIN ".main::$cnf['db_tables']['users']." AS u
			ON u.user_id = m.user_id
			WHERE m.vps_id = '".$vo_vps->get_vps_id()."' ".$where_statement."
		");

		$chk = $query->execute($where_values);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		foreach($query->fetchAll() AS $fetch){
			$result[] = new vo_users($fetch);
		}

		return $result;

	}

	public static function update($columns,$where_statement,$where_values=array()){

		$result = array();

		$update_data = "";
		$replace_data = array();
		foreach($columns AS $col => $value){
			$inc = isset($inc) ? "," : "";
			$update_data .= $inc."`".$col."`"." = :u_".$col." ";
			$replace_data["u_".$col] = $value;
		}

		$query = dev::$db->prepare("
			UPDATE
			".main::$cnf['db_tables']['vps']."
			SET
			".$update_data."
			".$where_statement."
		");

		$replace_data = array_merge($replace_data,$where_values);

		$chk = $query->execute($replace_data);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		return true;

	}

	public static function insert($columns){

		$result = array();

		$insert_cols = "";
		$insert_vals = "";
		$replace_data = array();
		foreach($columns AS $col => $value){
			$inc = isset($inc) ? "," : "";
			$insert_cols .= $inc.$col;
			$insert_vals .= $inc.":u_".$col;
			$replace_data["u_".$col] = $value;
		}

		$query = dev::$db->prepare("
			INSERT INTO
			".main::$cnf['db_tables']['vps']."
			(
				".$insert_cols."
			)
			VALUES
			(
				".$insert_vals."
			)
		");

		$chk = $query->execute($replace_data);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		return true;

	}

	public static function remove($where_statement,$where_values=array()){

		$result = array();

		$query = dev::$db->prepare("
			DELETE
			FROM ".main::$cnf['db_tables']['vps']."
			".$where_statement."
		");

		$chk = $query->execute($where_values);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		return true;

	}

}

?>
