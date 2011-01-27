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
	<input type="hidden" name="ip_pool_id" value="{ip_pool_id}" />
	<div class="body">
		<fieldset>
			<legend>IP Pool Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Pool Name</strong></div>
					<div><small>Ex: srv1.panenthe.com-1</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>First IP Address</strong></div>
					<div><small>Ex: 192.168.1.2</small></div>
				</td>
				<td><input type="text" name="first_ip" value="{first_ip}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Last IP Address</strong></div>
					<div><small>Ex: 192.168.1.17</small></div>
				</td>
				<td><input type="text" name="last_ip" value="{last_ip}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>DNS Entries (Space Seperated)</strong></div>
					<div><small>Ex: 208.67.220.220 208.67.222.222</smalll></div>
				</td>
				<td><input type="text" name="dns" value="{dns}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Gateway</strong></div>
					<div><small>Ex: 192.168.1.1</small></div>
				</td>
				<td><input type="text" name="gateway" value="{gateway}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Netmask</strong></div>
					<div><small>Ex: 255.255.255.0</small></div>
				</td>
				<td><input type="text" name="netmask" value="{netmask}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Servers</strong></div>
					<div><small>Select servers the IP pool will be available too.</small></div>
				</td>
				<td>{servers}</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save IP Pool" /></td>
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
			<input type="hidden" name="app" value="ip_pools" />
			<input type="hidden" name="sec" value="browse" />
			<td colspan="3">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="ip_pools" />
			<input type="hidden" name="sec" value="browse" />
			<td class="text_right" colspan="3" width="300">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>Pool ID</td>
			<td width="30%">Name</td>
			<td>First IP</td>
			<td>Last IP</td>
			<td>Assigned</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{ip_pool_id}" id="browse_action_{ip_pool_id}" class="browse_action" /></td>
			<td><label for="browse_action_{ip_pool_id}">{ip_pool_id}</label></td>
			<td>
				<a href="index.php?app=ip_pools&sec=update_ip_pool&ip_pool_id={ip_pool_id}">
					<strong>{name}</strong>
				</a>
			</td>
			<td><label for="browse_action_{ip_pool_id}">{first_ip}</label></td>
			<td><label for="browse_action_{ip_pool_id}">{last_ip}</label></td>
			<td><a href="index.php?app=ip_pools&sec=view_ip_pool&ip_pool_id={ip_pool_id}">{assigned}/{total}</a></td>
		</tr>
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="6"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<tr class="table_search">
			<td colspan="3">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove Ip Pools" />
			</td>
			<td class="text_right" colspan="3">
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

//---------------------------------------
//View IP Pool
//---------------------------------------
$templates['view_top'] = '
	<div class="body">
		<a href="index.php?app=ip_pools&sec=browse_ip_pools">Back to IP Pools</a>
	</div>
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">

		<tr class="table_desc">
			<td>IP Address</td>
			<td>Pool ID</td>
			<td width="30%">Pool Name</td>
			<td>VPS ID</td>
			<td>VPS Name</td>
		</tr>
';

$templates['view_row'] = '
		<tr class="browse_row{row}">
			<td><strong>{ip_addr}</strong></td>
			<td><a href="index.php?app=ip_pools&sec=update_ip_pool&ip_pool_id={ip_pool_id}">{ip_pool_id}</a></td>
			<td><a href="index.php?app=ip_pools&sec=update_ip_pool&ip_pool_id={ip_pool_id}">{name}</a></td>
			<td><a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">{vps_id}</a></td>
			<td><a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">{vps_name}</a></td>
		</tr>
';

$templates['view_row_none'] = '
		<tr>
			<td colspan="5"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['view_bot'] = '
		</table>
	</div>
';

?>
