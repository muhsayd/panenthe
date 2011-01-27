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

class vw_settings {

	public static function title($title){

		dev::$tpl->parse(
			'settings',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){

		dev::$tpl->parse(
			'settings',
			'form',
			$fields
		);
	}

	public static function confirmation(){
	
		$message = func_settings::get_message();
		
		dev::$tpl->parse(
			'settings',
			'confirmation',
			array(
				"message"	=>	$message
			)
		);
		
	}

}

?>
