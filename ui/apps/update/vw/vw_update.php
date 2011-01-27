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

class vw_update {

	public static function live_update(
		$current_version,
		$latest_version,
		$is_running,
		$release_news
	){

		$cparts = explode('.',trim($current_version));
		$lparts = explode('.',trim($latest_version));
		
		if($cparts[3] >= $lparts[3] || $is_running){
			$allow_update = ' disabled="disabled" ';
		}
		else
		{
			$allow_update = '';
		}

		dev::$tpl->parse(
			'update',
			'live_update',
			array(
				"allow_update"		=>	$allow_update,
				"current_version"	=>	$current_version,
				"latest_version"	=>	$latest_version,
				"release_news"		=>	$release_news
			)
		);
		
	}
	
}

?>
