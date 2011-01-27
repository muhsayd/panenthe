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

//Get Pool ID
if(!isset(main::$params['ip_pool_id']) || empty(main::$params['ip_pool_id'])){
	die("no_ip_pool_id");
}

//Get Pool
$rows = func_ip_pools::get_ip_pool_by_id(main::$params['ip_pool_id']);
if(!isset($rows[0])){
	die("no_ip_pool_found");
}

$pool = $rows[0];
echo main::output_from_vo($pool);

require_once(dirname(__FILE__)."/api_end.php");

?>
