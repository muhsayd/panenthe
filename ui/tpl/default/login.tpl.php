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

$templates['login'] = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Login | {site_name} Admin Panel</title>
	<script type="text/javascript">
		SITE_ROOT = "{site_root}";
		SITE_URL = "{site_url}";
		SITE_URI = "{site_uri}";
	</script>
    {script_javascript}
    {script_css}
</head>
<body>
<div class="admin_topbar">
	<div class="float_right">
		Visit <a href="http://www.panenthe.com">Panenthe.com</a>
	</div>
	Welcome to Panenthe!
</div>

<div class="wrapper">

	<div class="admin_header">
		<div class="header_logo">
			<div id="header_title">
				<a href="index.php"><img src="/theme/default/images/panenthe_logo.png" alt="{site_name}" /></a>
				<!--<h1 id="logo-text"><a href="index.php" title="">Pane<span>nthe</span></a></h1>		
				<h2 id="slogan">virtualization management solutions...</h2>-->
			</div>
		</div>
	</div>

	{action_message}
	<form action="" method="post" class="disableLoadingLink">
	<table cellpadding="0" cellspacing="0" class="login_table">
	<tr>
		<td colspan="2" class="center title">
			Login to {site_name} Admin Panel
		</td>
	</tr>
	<tr>
		<td><strong>Username</strong></td>
		<td><input type="text" name="staff_login" id="staff_login" /></td>
	</tr>
	<tr class="odd">
		<td><strong>Password</strong></td>
		<td><input type="password" name="staff_password" id="staff_password" /></td>
	</tr>
	<tr>
		<td class="login_button">
			<input type="submit" value="Login" />
		</td>
		<td class="forgot_password">
			{fp_link}
		</td>
	</tr>
	</table>
	</form>
</div>
</body>
</html>
';

$templates['fp_link'] = '
	<a href="index.php?app=login&sec=forgot_password">Forgot Password?</a>
';

$templates['forgot_password'] = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Login | {site_name} Admin Panel</title>
	<script type="text/javascript">
		SITE_ROOT = "{site_root}";
		SITE_URL = "{site_url}";
		SITE_URI = "{site_uri}";
	</script>
    {script_javascript}
    {script_css}
</head>
<body>
<div class="admin_topbar">
	<div class="float_right">
		Visit <a href="http://www.panenthe.com">Panenthe.com</a>
	</div>
	Welcome to Panenthe!
</div>

<div class="wrapper">

	<div class="admin_header">
		<div class="header_logo">
			<div id="header_title">
				<h1 id="logo-text"><a href="index.php" title="">Pane<span>nthe</span></a></h1>		
				<h2 id="slogan">virtualization management solutions...</h2>

			</div>
		</div>
	</div>

	{action_message}
	<form action="" method="post" class="disableLoadingLink">
	<input type="hidden" name="forgot_password" value="true" />
	<table cellpadding="0" cellspacing="0" class="login_table">
	<tr>
		<td colspan="2" class="center title">
			Forgot Password to {site_name} Admin Panel
		</td>
	</tr>
	<tr class="odd">
		<td><strong>Username</strong></td>
		<td><input type="text" name="staff_login" id="staff_login" /></td>
	</tr>
	<tr>
		<td colspan="2" class="login_button">
			<input type="submit" value="Reset Password" />
		</td>
	</tr>
	</table>
	</form>
</div>
</body>
</html>
';

$templates['forgot_password_confirmation'] = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Login | {site_name} Admin Panel</title>
	<script type="text/javascript">
		SITE_ROOT = "{site_root}";
		SITE_URL = "{site_url}";
		SITE_URI = "{site_uri}";
	</script>
    {script_javascript}
    {script_css}
</head>
<body>
<div class="admin_topbar">
	<div class="float_right">
		Visit <a href="http://www.panenthe.com">Panenthe.com</a>
	</div>
	Welcome to Panenthe!
</div>

<div class="wrapper">

	<div class="admin_header">
		<div class="header_logo">
			<div id="header_title">
				<h1 id="logo-text"><a href="index.php" title="">Pane<span>nthe</span></a></h1>		
				<h2 id="slogan">virtualization management solutions...</h2>

			</div>
		</div>
	</div>

	<table cellpadding="0" cellspacing="0" class="login_table">
	<tr>
		<td colspan="2" class="center title">
			Forgot Password to {site_name} Admin Panel
		</td>
	</tr>
	<tr class="odd" colspan="2">
		<td>Your new password has been emailed to you. Thanks!</td>
	</tr>
	<tr>
		<td colspan="2" class="login_button">
			<a href="index.php">Proceed to Login</a>
		</td>
	</tr>
	</table>
	</form>
</div>
</body>
</html>
';

$templates['password_reset_email'] = '
<div>Hello {first_name},</div>
<br />
<div>You have used our automated password reset system to reset your password.</div>
<br />
<div>Your new password is: <strong>{password}</div>
<br />
<div>Now you can login to the control panel at <a href="{site_url}">{site_url}</a></div>
<br />
<div>Thanks,</div>
<div>{site_name}</div>
';

$templates['message'] = '
<div class="login_message">{message}</div>
';

?>
