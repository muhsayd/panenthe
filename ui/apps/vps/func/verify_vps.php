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

class verify_vps {

	public static function insert(){

		$dataCheck = new dev_dataCheck();

		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'hostname':
					$fieldCheck = new dev_dataCheckField($field,'Host Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->maxLength(64);
					$fieldCheck->allowChars('0-9a-zA-Z@._-','alphanumeric with . _ - @');
					break;
					
				case 'name':
					$fieldCheck = new dev_dataCheckField($field,'VM Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->maxLength(64);
					$fieldCheck->allowChars('0-9a-zA-Z@._-','alphanumeric with . _ - @');
					break;

				case 'ost':
					$fieldCheck = new dev_dataCheckField($field,'OS Template',$value);
					$fieldCheck->notBlank();
					break;

				case 'server_id':
					$fieldCheck = new dev_dataCheckField($field,'Host Server',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'driver_id':
					$fieldCheck = new dev_dataCheckField($field,'VM Driver',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'confirm_root_password':
					$fieldCheck = new dev_dataCheckField($field,'Root Password Confirmation',$value);
					$fieldCheck->equals(dev::$post['root_password']);
					break;

				case 'username':
				
					if(dev::$post['user_add_type'] == 'assign_user'){
						break;
					}

					$fieldCheck = new dev_dataCheckField($field,'Username',$value);
					
					//Check for Duplicate
					$query = dev::$db->query("
						SELECT * FROM ".main::$cnf['db_tables']['users']."
						WHERE username = '".dev::$s_post[$field]."' 
					");
					if($query->rowCount() > 0 && $value != ''){
						$fieldCheck->manualFailure('[['.$field.']] is already taken.');
					}
					
					$fieldCheck->notBlank();
					$fieldCheck->minLength(4);
					$fieldCheck->maxLength(32);
					$fieldCheck->allowChars('0-9a-zA-Z.@_-','alphanumeric with . @ _ -');
					break;

				case 'confirm_password':
				
					if(dev::$post['user_add_type'] == 'assign_user'){
						break;
					}
					
					$fieldCheck = new dev_dataCheckField($field,'User Password Confirmation',$value);
					$fieldCheck->equals(dev::$post['password']);
					break;

				case 'email':
				
					if(dev::$post['user_add_type'] == 'assign_user'){
						break;
					}
					
					$fieldCheck = new dev_dataCheckField($field,'User Email Address',$value);
					
					//Check for Duplicate
					$query = dev::$db->query("
						SELECT * FROM ".main::$cnf['db_tables']['users']."
						WHERE email = '".dev::$s_post[$field]."'
					");
					if($query->rowCount() > 0){
						$fieldCheck->manualFailure('[['.$field.']] is already taken.');
					}
					$fieldCheck->notBlank();
					
					break;

				case 'first_name':
				
					if(dev::$post['user_add_type'] == 'assign_user'){
						break;
					}
					
					$fieldCheck = new dev_dataCheckField($field,'User First Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('alnums');
					break;

				case 'last_name':
				
					if(dev::$post['user_add_type'] == 'assign_user'){
						break;
					}
					
					$fieldCheck = new dev_dataCheckField($field,'User Last Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('alnums');
					break;

				case 'no_ips':
					$fieldCheck = new dev_dataCheckField($field,'# of IP\'s',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'manual_ips':
					$fieldCheck = new dev_dataCheckField($field,'Manual IP Assignment',$value);
					$fieldCheck->allowRawChars('0-9\.\s','numeric with periods (.) and spaces');
					break;

				case 'disk_space':
					$fieldCheck = new dev_dataCheckField($field,'Disk Space',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan('500');
					break;

				case 'backup_space':
					$fieldCheck = new dev_dataCheckField($field,'Backup Space',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'swap_sapce':
					$fieldCheck = new dev_dataCheckField($field,'Swap Space',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'g_mem':
					$fieldCheck = new dev_dataCheckField($field,'Guaranteed Memory',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan('64');
					break;

				case 'b_mem':
					$fieldCheck = new dev_dataCheckField($field,'Burstable Memory',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'cpu_pct':
					$fieldCheck = new dev_dataCheckField($field,'CPU Percentage',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan(10);
					$fieldCheck->lessThan(101);
					break;

				case 'cpu_num':
					$fieldCheck = new dev_dataCheckField($field,'CPU Multiplier',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan(0);
					break;

				case 'out_bw':
					$fieldCheck = new dev_dataCheckField($field,'Outgoing Traffic',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan(0);
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
	
	public static function name(){

		$dataCheck = new dev_dataCheck();

		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'hostname':
					$fieldCheck = new dev_dataCheckField($field,'Host Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->maxLength(64);
					$fieldCheck->allowChars('0-9a-zA-Z@._-','alphanumeric with . _ - @');
					break;

				case 'name':
					$fieldCheck = new dev_dataCheckField($field,'VM Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->maxLength(64);
					$fieldCheck->allowChars('0-9a-zA-Z@._-','alphanumeric with . _ - @');
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

	public static function limits(){

		$dataCheck = new dev_dataCheck();

		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'disk_space':
					$fieldCheck = new dev_dataCheckField($field,'Disk Space',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan('500');
					break;

				case 'backup_space':
					$fieldCheck = new dev_dataCheckField($field,'Backup Space',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'swap_sapce':
					$fieldCheck = new dev_dataCheckField($field,'Swap Space',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'g_mem':
					$fieldCheck = new dev_dataCheckField($field,'Guaranteed Memory',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan('64');
					break;

				case 'b_mem':
					$fieldCheck = new dev_dataCheckField($field,'Burstable Memory',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					break;

				case 'cpu_pct':
					$fieldCheck = new dev_dataCheckField($field,'CPU Percentage',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan(10);
					$fieldCheck->lessThan(101);
					break;

				case 'cpu_num':
					$fieldCheck = new dev_dataCheckField($field,'CPU Multiplier',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan(0);
					break;

				case 'out_bw':
					$fieldCheck = new dev_dataCheckField($field,'Outgoing Traffic',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('num');
					$fieldCheck->greaterThan(0);
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


	public static function root_password(){

		$dataCheck = new dev_dataCheck();

		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'confirm_root_password':
					$fieldCheck = new dev_dataCheckField($field,'Root Password Confirmation',$value);
					$fieldCheck->equals(dev::$post['root_password']);
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
