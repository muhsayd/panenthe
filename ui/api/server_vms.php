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
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps.php');

//Get VPS ID
if(!isset(main::$params['server_id']) || empty(main::$params['server_id'])){
	die("no_server_id");
}

//Get Server
$rows = func_servers::get_server_by_id(main::$params['server_id']);
if(!isset($rows[0])){
	die("no_server_found");
}

$rows = func_vps::get_vps(
	"server_id = :v_server_id",
	array(":v_server_id"=>main::$params['server_id'])
);

foreach($rows AS $vo_vps){
	if(isset($inc)){
		$inc = "###";
	}else{
		$inc = "";
	}
	echo $inc;
	main::$params['vps_id'] = $vo_vps->get_vps_id();
	include("vm_get_info.php");
}

require_once(dirname(__FILE__)."/api_end.php");

?>
