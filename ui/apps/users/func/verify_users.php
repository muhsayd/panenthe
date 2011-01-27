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

class verify_users {

	public static function insert(){

		$dataCheck = new dev_dataCheck();

		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){

				case 'username':

					$fieldCheck = new dev_dataCheckField($field,'Username',$value);
					
					if(empty(dev::$post["user_id"])){
						//Check for Duplicate
						$query = dev::$db->query("
							SELECT * FROM ".main::$cnf['db_tables']['users']."
							WHERE username = '".dev::$s_post[$field]."' 
						");
						if($query->rowCount() > 0){
							$fieldCheck->manualFailure('[['.$field.']] is already taken.');
						}
					}
					
					$fieldCheck->notBlank();
					$fieldCheck->minLength(4);
					$fieldCheck->maxLength(32);
					$fieldCheck->allowChars('0-9a-zA-Z.@_-','alphanumeric with . @ _ -');
					break;

				case 'confirm_password':
					$fieldCheck = new dev_dataCheckField($field,'User Password Confirmation',$value);
					$fieldCheck->equals(dev::$post['password']);
					break;

				case 'email':
					$fieldCheck = new dev_dataCheckField($field,'User Email Address',$value);
					
					$fieldCheck->notBlank();
					if(empty(dev::$post["user_id"])){
					
						//Check for Duplicate
						$query = dev::$db->query("
							SELECT * FROM ".main::$cnf['db_tables']['users']."
							WHERE email = '".dev::$s_post[$field]."'
						");
						if($query->rowCount() > 0 && $value != ''){
							$fieldCheck->manualFailure('[['.$field.']] is already taken.');
						}
					}
					
					break;

				case 'first_name':
					$fieldCheck = new dev_dataCheckField($field,'User First Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('alnums');
					break;

				case 'last_name':
					$fieldCheck = new dev_dataCheckField($field,'User Last Name',$value);
					$fieldCheck->notBlank();
					$fieldCheck->isType('alnums');
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
