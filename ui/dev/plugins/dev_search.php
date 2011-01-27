<?php
/**
 * Panenthe
 *
 * Very light PHP Framework
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

class dev_search {

	static $column;
	static $keywords;
	static $sql;
	static $sql_value;

	public static function set_keywords($keywords){
		self::$keywords = $keywords;
	}

	public static function set_column($column){
		self::$column = $column;
	}

	public static function generate(){
		self::generate_sql();
		self::generate_sql_value();
	}

	public static function generate_sql(){

		if(empty(self::$keywords)){
			self::$sql = '';
			return;
		}
		
		self::$sql = self::$column.' LIKE :u_'.self::$column;
		
	}

	public static function generate_sql_value(){
		self::$sql_value = self::$keywords;
	}

	public static function get_sql(){
		return self::$sql;
	}

	public static function get_sql_value(){
		if(empty(self::$keywords)){
			return array();
		}
		return array('u_'.self::$column=>'%'.self::$sql_value.'%');
	}

}

?>
