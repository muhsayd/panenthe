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

$templates['system_stats'] = '
					<div class="title">System Stats</div>
					<div class="body">
						<table cellpadding="5" cellspacing="0" width="100%">
						<tr class="odd">
							<td>Licensed VMs</td>
							<td class="text_right">{licensed_vms}</td>
						</tr>
						<tr class="even">
							<td><a href="{site_url}/index.php?app=vps&sec=browse_vps" class="disableLoadingLink">Total VMs</a></td>
							<td class="text_right">{total_vms}</td>
						</tr>
						<tr class="odd">
							<td><a href="{site_url}/index.php?app=update" class="disableLoadingLink">Current Version</a></td>
							<td class="text_right">{script_version}</td>
						</tr>
						<tr class="even">
							<td><a href="{site_url}/index.php?app=update" class="disableLoadingLink">Latest Version</a></td>
							<td class="text_right">{latest_version}</td>
						</tr>
						<tr class="odd">
							<td><a href="{site_url}/index.php?app=servers&sec=browse_servers" class="disableLoadingLink">Servers</a></td>
							<td class="text_right">{total_servers}</td>
						</tr>
						<tr class="even">
							<td><a href="{site_url}/index.php?app=events&sec=browse_events" class="disableLoadingLink">Events</a></td>
							<td class="text_right">{unacknowledged_events}</td>
						</tr>
						<tr class="odd">
							<td><a href="{site_url}/index.php?app=users&sec=browse_clients" class="disableLoadingLink">Clients</a></td>
							<td class="text_right">{total_clients}</td>
						</tr>
						<tr class="even">
							<td><a href="{site_url}/index.php?app=users&sec=browse_staff" class="disableLoadingLink">Staff</a></td>
							<td class="text_right">{total_staff}</td>
						</tr>
						<tr class="odd">
							<td><a href="{site_url}/index.php?app=users&sec=browse_clients_online" class="disableLoadingLink">Clients Online</a></td>
							<td class="text_right">{clients_online}</td>
						</tr>
						<tr class="even">
							<td><a href="{site_url}/index.php?app=users&sec=browse_staff_online" class="disableLoadingLink">Staff Online</a></td>
							<td class="text_right">{staff_online}</td>
						</tr>
						</table>
					</div>
';

?>
