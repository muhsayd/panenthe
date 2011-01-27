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

$templates['welcome_email_form'] = '
	
	<div class="title">Welcome Email</div>
	<form action="" method="post">
	<input type="hidden" name="action" value="test_email" />
	<div class="body">
		<table cellpadding="10" cellspacing="0" width="100%">
		<tr class="odd">
			<td>
				<div><strong>Email Address</strong></div>
				<div><small>The address the test email should be sent to.</small></div>
			</td>
			<td>
				<input type="text" name="email" />
			</td>
		</tr>
		</table>
	</div>
	<div class="body">
		<input type="submit" value="Send Test Email" />
	</div>
	</form>
	
	<form action="" method="post">
	<input type="hidden" name="action" value="do_update" />
	<div class="body">
		<table cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="70%">
				<textarea name="welcome_email" style="width: 100%; height: 500px;">{welcome_email}</textarea>
			</td>
			<td valign="top">
				<div><strong>Available Vars</strong></div>
				<br />
				<div><strong>[[first_name]]</strong> - Users First Name</div>
				<div><strong>[[last_name]]</strong> - Users Last Name</div>
				<div><strong>[[hostname]]</strong> - VPS Hostname</div>
				<div><strong>[[site_url]]</strong> - URL to Panenthe Install</div>
				<div><strong>[[username]]</strong> - Users username</div>
				<div><strong>[[password]]</strong> - Users password</div>
				<div><strong>[[ip_address]</strong> - Main IP address of the VPS</div>
				<div><strong>[[root_password]]</strong> - Root password of the VPS</div>
				<div><strong>[[ost]]</strong> - OS Template installed on the VPS</div>
				<div><strong>[[disk_space]]</strong> - VPS disk space</div>
				<div><strong>[[backup_space]]</strong> - VPS backup space</div>
				<div><strong>[[swap_space]]</strong> - VPS swap space</div>
				<div><strong>[[g_mem]]</strong> - VPS guaranteed memory</div>
				<div><strong>[[b_mem]]</strong> - VPS burstable memory</div>
				<div><strong>[[cpu_pct]]</strong> - VPS CPU Percentage</div>
				<div><strong>[[cpu_num]]</strong> - VPS CPU Multiplier</div>
				<div><strong>[[out_bw]]</strong> - VPS outgoing bandwidth</div>
				<div><strong>[[in_pw]]</strong> - VPS incoming bandwidth</div>
				<div><strong>[[site_name]]</strong> - Configured Site Name</div>
			</td>
		</tr>
		</table>
	</div>
	<div class="body">
		<input type="submit" value="Change Welcome Email" />
	</div>
	
';


?>
