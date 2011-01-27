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
require_once(SCRIPT_ROOT.'/lib/dao/dao_vps.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_vps.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_servers.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_servers.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/servers/func/func_servers.php');

//Get VPS ID
if(!isset(main::$params['server_id']) || empty(main::$params['server_id'])){
	die("no_server_id");
}

//Get Server
$ips = func_servers::get_server_ips(main::$params['server_id']);
if(!is_array($ips)){
	die("no_server_found");
}

$server = $ips;
foreach($server AS $key => $ip){
	if(isset($inc)){
		$inc = "&";
	}else{
		$inc = "";
	}
	echo $inc."ip[".$key."]=".$ip;
}

require_once(dirname(__FILE__)."/api_end.php");

?>
