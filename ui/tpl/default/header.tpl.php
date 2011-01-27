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

$templates['header'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{title_tags} | {site_name} Admin Panel</title>

	<script type="text/javascript">
		SITE_ROOT = "{site_root}";
		SITE_URL = "{site_url}";
		SITE_URI = "{site_uri}";
	</script>
	{script_javascript}
	{script_css}
</head>
<body>
<div id="wrapper">
		{admin_topbar}
		<div id="wrapper_content">
		<div class="admin_header">
			<div class="float_right">
				<div class="header_date">
					<span id="header_date_month">{header_date_month}</span>
					<span id="header_date_day">{header_date_day}</span>,
					<span id="header_date_year">{header_date_year}</span>
				</div>
				<div class="header_time">
					<span id="header_time_hour">{header_time_hour}</span>:<span id="header_time_minute">{header_time_minute}</span><span id="header_time_period">{header_time_period}</span>
				</div>
			</div>
			<div class="header_logo">
				<div id="header_title">
					<a href="index.php"><img src="{logo_url}" alt="{site_name}" /></a>
					<!--<h1 id="logo-text"><a href="index.php" title="">Pane<span>nthe</span></a></h1>		
					<h2 id="slogan">virtualization management solutions...</h2>-->
				</div>
			</div>
		</div>
		
		{admin_navbar}
		<div class="admin_content">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" id="sidebar" height="100%">
				<ul class="sideNav">
					{admin_subbar}
				</ul>
			</td>
			<td valign="top" class="main_content">
				{action_message}
';

$templates['admin_navbar'] = '
		<div class="admin_navbar">
			{admin_navbar}
		</div>
';

$templates['admin_subbar'] = '
		{admin_subbar}
';

$templates['staff_topbar'] = '
<div class="admin_topbar">
			<div class="float_right">
				Hello, {cur_admin_first_name} {cur_admin_last_name} |
				<a href="index.php?app=users&sec=update_user&user_id={cur_admin_id}" class="disableLoadingLink">User Preferences</a> |
				<a href="index.php?logout=true" class="disableLoadingLink">Logout</a>
			</div>
			<a href="index.php?app=settings" class="disableLoadingLink">Settings</a> |
            <a href="index.php?app=drivers" class="disableLoadingLink">Drivers</a> | 
            <a href="index.php?app=ost" class="disableLoadingLink">OS Templates</a>
		</div>
';

$templates['client_topbar'] = '
		<div class="admin_topbar">
			<div class="float_right">
				Hello, {cur_admin_first_name} {cur_admin_last_name} |
				<a href="index.php?app=users&sec=update_user&user_id={cur_admin_id}" class="disableLoadingLink">User Preferences</a> |
				<a href="index.php?logout=true" class="disableLoadingLink">Logout</a>
			</div>
			<a href="index.php?app=vps" class="disableLoadingLink">Home</a>
		</div>
		
';




?>
