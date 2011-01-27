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

//Define Fields
$fields = array(
	"name"			=>	"",
	"first_ip"		=>	"",
	"last_ip"		=>	"",
	"dns"			=>	"",
	"gateway"		=>	"",
	"netmask"		=>	""
);

foreach($fields AS $name => $value){
	if(!isset(main::$params[$name])){
		die("no_".$name."_found");
	} else {
		$fields[$name] = main::$params[$name];
	}
}

$vo_ip_pools = new vo_ip_pools($fields);
$vo_ip_pools->set_created(time());
$vo_ip_pools->set_modified(time());
dao_ip_pools::insert($vo_ip_pools->insert_array());

echo dev::$db->lastInsertId();

require_once(dirname(__FILE__)."/api_end.php");

?>
