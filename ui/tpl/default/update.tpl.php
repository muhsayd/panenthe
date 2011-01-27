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

$templates['live_update'] = '
	<div class="title">
		<a href="">{site_name} Live Update <small>(click to refresh)</small></a>
	</div>

	<form action="" method="post">
	<input type="hidden" name="action" value="do_update" />
	<div class="body">
		<div class="even">
			<div>Current Version: <strong>{current_version}</strong></div>
		</div>
		<div class="odd">
			<div>Latest Version: <strong>{latest_version}</strong></div>
		</div>
		<div class="even">
			<input type="submit" value="Update to {latest_version}" {allow_update} />
		</div>
	</div>
	</form>
	
	<div class="title">
		Release News & Information
	</div>
	
	<div class="body">
		{release_news}
	</div>

';

?>
