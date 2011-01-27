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

function error_parser($error_data){
	
	$errors = array();
	$error_data = file_get_contents($error_data);
	
	$lines = explode("\n",$error_data);

	if(count($lines) > 0){
		foreach($lines AS $line){
			if(isset($line{0}) && $line{0} != "#"){
				$parts = explode(' | ',$line);
				if(count($parts) == 5){
					if($parts[0] != ''){
						$errors[$parts[0]] = array(
							"code"		=>	$parts[0],
							"severity"	=>	$parts[1],
							"constant"	=>	$parts[2],
							"message"	=>	$parts[3],
							"url"		=>	$parts[4]
						);
					}
				}
			}
		}
	}

	return $errors;

}

$errors = error_parser(PANENTHE_ROOT.'/shared/etc/errors');

?>
