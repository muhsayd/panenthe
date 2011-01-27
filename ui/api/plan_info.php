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
require_once(SCRIPT_ROOT.'/lib/dao/dao_plans.php');
require_once(SCRIPT_ROOT.'/lib/vo/vo_plans.php');

//Load Fnctionality
require_once(SCRIPT_ROOT.'/apps/plans/func/func_plans.php');

//Get Plan ID
if(!isset(main::$params['plan_id']) || empty(main::$params['plan_id'])){
	die("no_plan_id");
}

//Get Plan
$rows = func_plans::get_plan_by_id(main::$params['plan_id']);
if(!isset($rows[0])){
	die("no_plan_found");
}

$plan = $rows[0];
echo main::output_from_vo($plan);

require_once(dirname(__FILE__)."/api_end.php");

?>
