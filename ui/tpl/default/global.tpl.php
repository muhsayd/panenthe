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

$templates = array();

$templates['message'] = '
	{action_message}
';

$templates['action_message'] = '
	<div class="message">{action_message}</div>
';

$templates['admin_navbar'] = '
	<a href="{url}"{selected}>{name}</a>
';

$templates['admin_subbar'] = '
	<li><a href="{url}"{selected}><img src="{icon}" alt="{name}" class="subbar_icon" /> {name}</a></li>
';

$templates['select'] = '
	<select name="{name}" id="{name}">
	{options}
	</select>
';

$templates['select_multiple'] = '
	<select name="{name}" multiple="multiple" style="width: 300px; height: 100px;">
	{options}
	</select>
';

$templates['select_row'] = '
	<option value="{value}"{selected}>{name}</option>
';

$templates['checkbox_row'] = '
	<div><input type="checkbox" name="{name}" value="{value}" {selected} /> <strong>{v_name}</strong></div>
';

$templates['disabled_input'] = '
	<input type="text" name="{name}" value="{value}" readonly="readonly" />
';

$templates['no_access'] = '
	<div class="title">Cannot Access this Area</div>
	<div class="body">
		According to your current permission setup you cannot access this area.
		If you feel you have reached this in error then please contact an
		administrator to correct this problem.
		<br /><br />
		This error message does not indicate any bug with the software.
	</div>
';

$templates['error_page'] = '
	<div class="title">System Error</div>
	<div class="body">
		<span style="color: red; font-weight: bold;">{message}</span>
		<br /><br />
		This error message does not indicate any bug with the software.
	</div>
';

$templates['notice_page'] = '
	<div class="title">System Notice</div>
	<div class="body">
		<span style="color: red; font-weight: bold;">{message}</span>
	</div>
';


?>
