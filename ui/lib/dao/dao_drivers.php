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

class dao_drivers {

	public static function select($where_statement,$where_values=array()){

		$result = array();

		$query = dev::$db->prepare("
			SELECT *
			FROM ".main::$cnf['db_tables']['drivers']."
			".$where_statement."
		");

		$chk = $query->execute($where_values);

		if(!$chk){
			dev::output_r($query->errorInfo());
		}

		foreach($query->fetchAll() AS $fetch){
			$result[] = new vo_drivers($fetch);
		}

		return $result;

	}

	public static function get_by_driver_id($driver_id){
		
		$rows = self::select(
			"WHERE driver_id = :v_driver_id",
			array(
				"v_driver_id"	=>	$driver_id
			)
		);
		
		if(isset($rows[0])){
			return $rows[0];
		} else {
			return false;
		}
		
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
			".main::$cnf['db_tables']['drivers']."
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
			".main::$cnf['db_tables']['drivers']."
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
			FROM ".main::$cnf['db_tables']['drivers']."
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
