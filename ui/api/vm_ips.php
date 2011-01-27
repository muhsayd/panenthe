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

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_ip.php');

//Get VPS ID
if(!isset(main::$params['vps_id']) || empty(main::$params['vps_id'])){
	die("no_vps_id");
}

//Get Server
$vo_vps = func_vps::get_vps_by_id(main::$params['vps_id']);
$vo_vps = $vo_vps[0];

if(!is_object($vo_vps)){
	die("no_vps_found");
}

$ips = func_vps_ip::get_all_ips($vo_vps);
foreach($ips AS $key => $ip){
	if(isset($inc)){
		$inc = "&";
	}else{
		$inc = "";
	}
	echo $inc."ip[".$key."]=".$ip['ip'];
}

require_once(dirname(__FILE__)."/api_end.php");

?>
