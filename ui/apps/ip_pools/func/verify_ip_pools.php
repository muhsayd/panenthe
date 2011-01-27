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

class verify_ip_pools {

	public static function insert(){

		$dataCheck = new dev_dataCheck();

		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'name':
					$fieldCheck = new dev_dataCheckField($field,'Pool Name',$value);
					$fieldCheck->notBlank();
					break;
					
				case 'first_ip':
					$fieldCheck = new dev_dataCheckField($field,'First IP',$value);
					$fieldCheck->notBlank();
					$fieldCheck->allowChars('0-9.','numbers and periods');
					break;
					
				case 'last_ip':
					$fieldCheck = new dev_dataCheckField($field,'Last IP',$value);
					$fieldCheck->notBlank();
					$fieldCheck->allowChars('0-9.','numbers and periods');
					
					$first_ip = explode('.',dev::$post['first_ip']);
					$last_ip = explode('.',dev::$post['last_ip']);

					if(
						count($first_ip) != 4 || 
						count($last_ip) != 4
					){
						$fieldCheck->manualFailure('First or last ip is not valid. Must be x.x.x.x');
						break;
					}
					
					if(
						$first_ip[0] > $last_ip[0] || 
						$first_ip[1] > $last_ip[1] || 
						$first_ip[2] > $last_ip[2] ||
						$first_ip[3] > $last_ip[3]
					){
						$fieldCheck->manualFailure('IP range is invalid, must be positive.');
					}
					
					break;
					
				case 'dns':
					$fieldCheck = new dev_dataCheckField($field,'DNS Entries',$value);
					$fieldCheck->notBlank();
					break;
					
				case 'gateway':
					$fieldCheck = new dev_dataCheckField($field,'Gateway',$value);
					$fieldCheck->notBlank();
					$fieldCheck->allowChars('0-9.','numbers and periods');
					break;
					
				case 'netmask':
					$fieldCheck = new dev_dataCheckField($field,'Netmask',$value);
					$fieldCheck->notBlank();
					$fieldCheck->allowChars('0-9.','numbers and periods');
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
