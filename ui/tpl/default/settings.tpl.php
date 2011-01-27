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

$templates['title'] = '

';

$templates['form'] = '
	<form action="" method="post">
	<input type="hidden" name="form_action" value="save_settings" />
	<div class="body">
	
		<fieldset>
			<legend>Main Settings</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="40%">
					<div><strong>License User</strong></div>
					<div>
						<small>
							This is the username used to login to the Panenthe client 
							area and is used to update the licensing information.
						</small>
					</div>
				</td>
				<td>
					<input type="text" name="license_user" value="{license_user}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>License Password</strong></div>
					<div><small>The password to accompany the license user.</small></div>
				</td>
				<td>
					<input type="password" name="license_pass" value="" /> (blank for no change.)
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save Main Settings" /></td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>UI Settings</legend>
			
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="40%">
					<div><strong>Site Name</strong></div>
					<div><small>The name used to identify the installation.</small></div>
				</td>
				<td>
					<input type="text" name="site_name" value="{site_name}" />
				</td>
			<tr class="even">
				<td>
					<div><strong>Logo URL</strong></div>
					<div><small>URL to the logo that is displayed at the top left.</small></div>
				</td>
				<td>
					<input type="text" name="logo_url" value="{logo_url}" size="40" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>Head Title</strong></div>
					<div><small>This is the value displayed in the browsers title bar.</small></div>
				</td>
				<td>
					<input type="text" name="head_title" value="{head_title}" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>HTTP URL</strong>
					<div><small>This is the desired URL to the installation in a non SSL environment, blank will autodetect.</small></div>
				</td>
				<td>
					<input type="text" name="url" value="{url}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>SSL URL</strong></div>
					<div><small>This is the URL to the instance with SSL applied.</small></div>
				</td>
				<td>
					<input type="text" name="ssl_url" value="{ssl_url}" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>Login URL</strong></div>
					<div><small>This is the login url to Panenthe, this must be filled in for welcome emails.</small></div>
				</td>
				<td>
					<input type="text" name="login_url" value="{login_url}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>SSL Login URL</strong></div>
					<div><small>Same as the login url but with SSL applied.</small></div>
				</td>
				<td>
					<input type="text" name="ssl_login_url" value="{ssl_login_url}" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>Forgot Password</strong></div>
					<div><small>Enable forgot password functionality.</small></div>
				</td>
				<td>
					<input type="checkbox" name="forgot_password" {forgot_password} value="true" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>Max Failed Login Attempts</strong></div>
					<div><small>The amount of times a user/admin is allowed to fail logging in.</small></div>
				</td>
				<td>
					<input type="text" name="max_failed_login_attempts" size="4" value="{max_failed_login_attempts}" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>Failed Login Lockout Time (minutes)</strong></div>
					<div><small>This is the time in minutes that users will be denied access after breaching the max failed login attempts.</small></div>
				</td>
				<td>
					<input type="text" name="failed_login_lockout" size="4" value="{failed_login_lockout}" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save UI Settings" /></td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>API Settings</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="40%">
					<div><strong>API User</strong></div>
					<div><small>This is the user that is allowed to access the API</small></div>
				</td>
				<td>
					<input type="text" name="api_user" value="{api_user}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>API Password</strong></div>
					<div><small>This is the API password for the user that is allowed access.</small></div>
				</td>
				<td>
					<input type="password" name="api_pass" value="" /> (leave blank for no change.)
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save API Settings" /></td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>Mail Settings</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="40%">
					<div><strong>Default From</strong></div>
					<div><small>This is the from address listed for all emails sent from the system.</small></div>
				</td>
				<td>
					<input type="text" name="default_from" value="{default_from}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>Default Reply To</strong></div>
					<div><small>This is the reply to address that is used on all emails sent from the system.</small></div>
				</td>
				<td>
					<input type="text" name="default_replyto" value="{default_replyto}" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>Enable SMTP</strong></div>
					<div><small>By default email sends through sendmail, if an SMTP server is needed check this.</small></div>
				</td>
				<td>
					<input type="checkbox" name="smtp_enable" {smtp_enable} value="true" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>SMTP Host</strong></div>
					<div><small>When SMTP is enabled this host is used for the server.</small></div>
				</td>
				<td>
					<input type="text" name="smtp_host" value="{smtp_host}" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>SMTP Port</strong></div>
					<div><small>When SMTP is enabled this port is used for the server.</small>
				</td>
				<td>
					<input type="text" size="3" name="smtp_port" value="{smtp_port}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>SMTP Authentication</strong></div>
					<div><small>Some SMTP servers require authentication, if it is needed check this.</small></div>
				</td>
				<td>
					<input type="checkbox" name="smtp_auth" {smtp_auth} value="true" />
				</td>
			</tr>
			<tr class="even">
				<td>
					<div><strong>SMTP Username</strong></div>
					<div><small>When SMTP authentication is enabled this is the username used.</small></div>
				</td>
				<td>
					<input type="text" name="smtp_user" value="{smtp_user}" />
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>SMTP Password</strong></div>
					<div><small>When SMTP authentication is enabled this is the password used.</small></div>
				</td>
				<td>
					<input type="password" name="smtp_pass" value="" /> (leave blank for no change.)
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save Mail Settings" /></td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>Actions</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><input type="submit" value="Save All Settings" /></td>
			</tr>
			</table>
		</fieldset>
	</div>
	</form>
';

$templates['confirmation'] = '
	<div class="title">Modify Settings</div>
	<div class="body">
		<div>{message}</div>
		<br />
		<div><strong>Note:</strong> Settings will be applied on the next pageload.</div>
		<br />
		<div><a href="index.php?app=settings">Back to Settings</a></div>
	</div>
';

?>
