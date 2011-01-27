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
	<input type="hidden" name="plan_id" value="{plan_id}" />
	<div class="body">
		<fieldset>
			<legend>Plan Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Plan Name</strong></div>
					<div><small>Ex: VPS Starter</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Disk Space (MB)</strong></div>
					<div><small>Ex: 30000</small></div>
				</td>
				<td><input type="text" name="disk_space" value="{disk_space}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Backup Space (MB)</strong></div>
					<div><small>0 to disabled. Ex: 15000</small></div>
				</td>
				<td><input type="text" name="backup_space" value="{backup_space}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Swap Space (MB)</strong></div>
					<div><small>Not supported by all drivers. Ex: 512</small></div>
				</td>
				<td><input type="text" name="swap_space" value="{swap_space}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Guaranteed Memory (MB)</strong></div>
					<div><small>Drivers that dont support burstable memory will use this value. Ex: 512</small></div>
				</td>
				<td><input type="text" name="g_mem" value="{g_mem}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Burstable Memory (MB)</strong></div>
					<div><small>Not supported by all drivers. Ex: 512</small></div>
				</td>
				<td><input type="text" name="b_mem" value="{b_mem}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>CPU Percentage</strong></div>
					<div><small>Percent of total CPU allowed. Ex: 75</small></div>
				</td>
				<td><input type="text" name="cpu_pct" value="{cpu_pct}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>CPU Multiplier</strong></div>
					<div><small>If the server has multiple cores, this will multiply the percentage to enable multiple core use. Ex: 2</small></div>
				</td>
				<td><input type="text" name="cpu_num" value="{cpu_num}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Outgoing Traffic (GB)</strong></div>
					<div><small>Data downloaded from the server by visitors. Ex: 30</small></div>
				</td>
				<td><input type="text" name="out_bw" value="{out_bw}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Incoming Traffic (GB)</strong></div>
					<div><small>Data download by the server from other srouces. Ex 15</small></div>
				</td>
				<td><input type="text" name="in_bw" value="{in_bw}" size="40" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save plan" /></td>
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
			<input type="hidden" name="app" value="plans" />
			<input type="hidden" name="sec" value="browse" />
			<td colspan="4">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="plans" />
			<input type="hidden" name="sec" value="browse" />
			<td class="text_right" width="300" colspan="4">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>Plan ID</td>
			<td>Name</td>
			<td>Disk (MB)</td>
			<td>Backup (MB)</td>
			<td>Guar. Mem (MB)</td>
			<td>Burst Mem (MB)</td>
			<td>BW Out</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{plan_id}" id="browse_action_{plan_id}" class="browse_action" /></td>
			<td><label for="browse_action_{plan_id}">{plan_id}</label></td>
			<td>
				<a href="index.php?app=plans&sec=update_plan&plan_id={plan_id}">
					<strong>{name}</strong>
				</a>
			</td>
			<td><label for="browse_action_{plan_id}">{disk_space}</label></td>
			<td><label for="browse_action_{plan_id}">{backup_space}</label></td>
			<td><label for="browse_action_{plan_id}">{g_mem}</label></td>
			<td><label for="browse_action_{plan_id}">{b_mem}</label></td>
			<td><label for="browse_action_{plan_id}">{out_bw}</label></td>
		</tr>
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="8"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<tr class="table_search">
			<td colspan="4">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove Plans" />
			</td>
			<td class="text_right" colspan="4">
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
