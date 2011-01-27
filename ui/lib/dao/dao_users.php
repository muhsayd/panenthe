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

class dao_users {

	public static function select($where_statement,$where_values=array(),$orphaned=false){

		$result = array();

		if($orphaned){
			$sql = "
				SELECT ".main::$cnf['db_tables']['users'].".*, COUNT(v.vps_id) AS vps_count
				FROM ".main::$cnf['db_tables']['users']."
				LEFT JOIN ".main::$cnf['db_tables']['vps_user_map']." AS v
				ON v.user_id = ".main::$cnf['db_tables']['users'].".user_id
				".$where_statement."
			";
		} else {
			$sql = "
				SELECT *
				FROM ".main::$cnf['db_tables']['users']."
				".$where_statement."
			";
		}
		
		$query = dev::$db->prepare($sql);
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
			".main::$cnf['db_tables']['users']."
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
			".main::$cnf['db_tables']['users']."
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
			FROM ".main::$cnf['db_tables']['users']."
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
