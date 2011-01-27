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

class verify_ost {

	public static function insert(){

		$dataCheck = new dev_dataCheck();
		
		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'name':
					$fieldCheck = new dev_dataCheckField($field,'Pool Name',$value);
					$fieldCheck->notBlank();
					break;
					
				case 'path':
					$fieldCheck = new dev_dataCheckField($field,'Path',$value);
					$fieldCheck->notBlank();
					break;
					
					
			}

			if(is_object($fieldCheck)){
				$dataCheck->addCheck($field,$fieldCheck);
			}

			unset($fieldCheck);

		}
		
		//Execute Check
		$dataCheck->executeChecks();

		if($dataCheck->isOkay()){
			return true;
		}
		else
		{
			main::set_action_message($dataCheck->getOutput());
			return false;
		}
		
	}


}

?>
