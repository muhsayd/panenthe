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

class verify_plans {

	public static function insert(){

		$dataCheck = new dev_dataCheck();
		
		foreach(dev::$post AS $field => $value){
			
			$fieldCheck = false;
			
			switch($field){
			
				case 'name':
					$fieldCheck = new dev_dataCheckField($field,'Plan Name',$value);
					$fieldCheck->notBlank();
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


}

?>
