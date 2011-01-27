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

define("IN_API",true);
require_once(dirname(__FILE__)."/api_common.php");

//Load Libs
require_once(SCRIPT_ROOT.'/lib/dao/dao_ip_pools.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_ip_pools.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/ip_pools/func/func_ip_pools.php');

//Get Server
$rows = func_ip_pools::get_ip_pools(
	"",
	array()
);

if(!isset($rows[0])){
	die("no_ip_pools_found");
}

foreach($rows AS $ip_pool){
	if(isset($inc)){
		$inc = "###";
	}
	else
	{
		$inc = "";
	}
	echo $inc;
	echo main::output_from_vo($ip_pool);
}

require_once(dirname(__FILE__)."/api_end.php");

?>
