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

//Get Config
require('/opt/panenthe/shared/core/config_parser.php');

//Get Errors
require('/opt/panenthe/shared/core/error_parser.php');

//Translate Config
$config['site_name'] = $config['ui_config']['site_name'];
$config['head_title'] = $config['ui_config']['head_title'];
$config['url'] = $config['ui_config']['url'];
$config['uri'] = $config['ui_config']['uri'];
$config['login_url'] = $config['ui_config']['login_url'];
$config['session_name'] = $config['ui_config']['session_name'];
$config['items_per_page'] = $config['ui_config']['items_per_page'];
$config['dev']['tpl']['tpl_path'] = $config['ui_config']['tpl_path'];
$config['dev']['tpl']['tpl_theme'] = $config['ui_config']['tpl_theme'];
$config['dev']['db'] = $config['db'];
$config['dev']['mail'] = $config['mail'];

?>
