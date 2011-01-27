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
	<input type="hidden" name="driver_id" value="{driver_id}" />
	<div class="body">
		<fieldset>
			<legend>Driver Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Driver System Name</strong></div>
					<div><small>This is the system name of the driver. Usually the name of the folder in /opt/panenthe/drivers/xxx</small></div>
				</td>
				<td><input type="text" name="ext_ref" value="{ext_ref}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Name</strong></div>
					<div><small>Enter the name of the driver used in the interface. Ex: OpenVZ</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Version</strong></div>
					<div><small>Enter Version of the driver installed. This helps tracking.</small></div>
				</td>
				<td><input type="text" name="version" value="{version}" size="40" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save driver" /></td>
			</tr>
			</table>
		</fieldset>
	</div>
	</form>
';

$templates['browse_top'] = '
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<!--<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="drivers" />
			<input type="hidden" name="sec" value="browse" />
			<td colspan="3">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="drivers" />
			<input type="hidden" name="sec" value="browse" />
			<td class="text_right" width="300" colspan="3">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>-->

		<tr class="table_desc">
			<!--<td></td>-->
			<td>Driver ID</td>
			<td>System Name</td>
			<td>Name</td>
			<td>Version</td>
			<td>Driver Options</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<!--<td><input type="checkbox" name="browse_action[]" value="{driver_id}" id="browse_action_{driver_id}" class="browse_action" /></td>-->
			<td><label for="browse_action_{driver_id}">{driver_id}</label></td>
			<td>
				<!--<a href="index.php?app=drivers&sec=update_driver&driver_id={driver_id}">-->
					<strong>{ext_ref}</strong>
				<!--</a>-->
			</td>
			<td><label for="browse_action_{driver_id}">{name}</label></td>
			<td><label for="browse_action_{driver_id}">{version}</label></td>
			<td>
				<!--<a href="index.php?app=drivers&sec=remove_driver&remove_driver={driver_id}" class="actionLink">Delete</a>-->
				(none)
			</td>
		</tr>
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="6"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<!--<tr class="table_search">
			<td colspan="3">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove Drivers" />
			</td>
			<td class="text_right" colspan="3">
				{pagination_html}
			</td>
		</tr>-->
		</form>
		</table>
	</div>
';

$templates['browse_search_select_row'] = '
		<option value="{col}"{selected}>{ver}</option>
';

$templates['browse_items_page_select_row'] = '
		<option value="{col}"{selected}>{ver}</option>
';



?>
