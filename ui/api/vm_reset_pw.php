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
require_once(SCRIPT_ROOT.'/lib/dao/dao_drivers.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_drivers.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_events.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_events.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_ost.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_ost.php');
require_once(SCRIPT_ROOT.'/lib/dao/dao_servers.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_servers.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/drivers/func/func_drivers.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/func_vps_ip.php');
require_once(SCRIPT_ROOT.'/apps/vps/func/vps_operations.php');
require_once(SCRIPT_ROOT.'/apps/events/func/event_api.php');

//Get VPS ID
if(!isset(main::$params['vps_id']) || empty(main::$params['vps_id'])){
	die("no_vps_id");
}

//Get VPS
$rows = func_vps::get_vps_by_id(main::$params['vps_id']);
if(!isset($rows[0])){
	die("no_vps_found");
}

//Get New Password
if(!isset(main::$params['passwd']) || empty(main::$params['passwd'])){
	die("no_password");
} else {
	$password = main::$params['passwd'];
}

$vps = $rows[0];
$vo_vps =& $vps;

$vpsDriver = new vps_operations($vo_vps);
$vpsDriver->set_passwd('root',$password);
$vpsDriver->execute();

if($vpsDriver->isOkay()){
	echo 0;
}
else
{
	echo 1;
	echo $vpsDriver->getOutput();
}

require_once(dirname(__FILE__)."/api_end.php");

?>
