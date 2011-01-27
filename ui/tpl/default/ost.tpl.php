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
	<input type="hidden" name="ost_id" value="{ost_id}" />
	<div class="body">
		<fieldset>
			<legend>Operating System Template Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Name</strong></div>
					<div><small>The name is used to reference the OS Template in the interface.</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Path</strong></div>
					<div><small>Absolute path to the template. Ex: /opt/ost/ovz/CentOS-5-x86.tar.gz</small></div>
				</td>
				<td><input type="text" name="path" value="{path}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>OS Type</strong></div>
				</td>
				<td>
					{os_type}
				</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Driver</strong></div>
					<div><small>Select the driver that this OS Template is associated with.</small></div>
				</td>
				<td>
					{driver_id}
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Architecture</strong></div>
					<div><small>Select the template architecture.</small></div>
				</td>
				<td>
					{arch}
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save OS Template" /></td>
			</tr>
			</table>
		</fieldset>
	</div>
	</form>
';

$templates['browse_top'] = '
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="ost" />
			<input type="hidden" name="sec" value="browse" />
			<td colspan="5">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="ost" />
			<input type="hidden" name="sec" value="browse" />
			<td class="text_right" width="350" colspan="2">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>Ost ID</td>
			<td width="25%">Name</td>
			<td>Path</td>
			<td>Type</td>
			<td>Driver</td>
			<td>Arch</div>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{ost_id}" id="browse_action_{ost_id}" class="browse_action" /></td>
			<td><label for="browse_action_{ost_id}">{ost_id}</label></td>
			<td>
				<a href="index.php?app=ost&sec=update_ost&ost_id={ost_id}">
					<strong>{name}</strong>
				</a>
			</td>
			<td><label for="browse_action_{ost_id}">{path}</label></td>
			<td><label for="browse_action_{ost_id}">{os_type}</label></td>
			<td><label for="browse_action_{ost_id}">{driver_id}</label></td>
			<td><lanel for="browse_action_{ost_id}">{arch}</label></td>
		</tr>
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="7"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<tr class="table_search">
			<td colspan="5">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove Ost" />
			</td>
			<td class="text_right" colspan="2">
				{pagination_html}
			</td>
		</tr>
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
