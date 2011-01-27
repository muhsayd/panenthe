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

$templates['view_logs'] = '
	<div class="body">
	
		<fieldset>
			<legend>Action Log</legend>
			<textarea name="action_log" class="log_area">{action}</textarea>
		</fieldset>
		
		<fieldset>
			<legend>Error Log</legend>
			<textarea name="error_log" class="log_area">{error}</textarea>
		</fieldset>
		
	</div>
';

?>
