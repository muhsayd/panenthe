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
	<input type="hidden" name="server_id" value="{server_id}" />
	<div class="body">
		<fieldset>
			<legend>Server Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Server Name</strong></div>
					<div><small>Usually the hostname for reference. Ex: srv1.panenthe.com</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Datacenter</strong></div>
					<div><small>Location where the server is located.</small></div>
				</td>
				<td><input type="text" name="datacenter" value="{datacenter}" size="40" /.</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Server IP Address</strong></div>
					<div><small>IP address used to access the server. This can also be a domain name. Ex: srv1.panenthe.com</small>
				</td>
				<td><input type="text" name="ip" value="{ip}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Server Hostname</strong></div>
					<div><small>Hostname of the server. Ex: srv1.panenthe.com</small>
				</td>
				<td><input type="text" name="hostname" value="{hostname}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Server Port</strong></div>
					<div><small>SSH Port of the server. Ex: 22</small>
				</td>
				<td><input type="text" name="port" value="{port}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Server Password</strong></div>
					<div><small>Root password of server. Used for installation and key generation. It is then discarded.</small></div>
				</td>
				<td><input type="text" name="password" value="{password}" size="40" autocomplete="off" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Select Master</strong></div>
					<div><small>Master server is the controller of the slave nodes.</small></div>
				</td>
				<td>
					{parent_server_id}
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save server" /></td>
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
			<input type="hidden" name="app" value="servers" />
			<input type="hidden" name="sec" value="browse" />
			<td colspan="4">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="servers" />
			<input type="hidden" name="sec" value="browse" />
			<td class="text_right" width="300" colspan="3">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>Server ID</td>
			<td>Name</td>
			<td>Hostname</td>
			<td>Datacenter</td>
			<td>IP</td>
			<td>Created</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{server_id}" id="browse_action_{server_id}" class="browse_action" /></td>
			<td><label for="browse_action_{server_id}">{server_id}</label></td>
			<td>
				<a href="index.php?app=servers&sec=server_home&server_id={server_id}">
					<strong>{name}</strong>
				</a>
			</td>
			<td><lanel for="browse_action_{server_id}">{hostname}</label></td>
			<td><label for="browse_action_{server_id}">{datacenter}</label></td>
			<td><label for="browse_action_{server_id}">{ip}</label></td>
			<td><label for="browse_action_{server_id}">{created}</label></td>
		</tr>
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="7"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<tr class="table_search">
			<td colspan="4">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" class="v_middle" />
				<input type="submit" name="browse_action_delete" value="Remove Servers" class="v_middle" />
				<input type="checkbox" name="force_delete" value="true" id="force_delete" class="v_middle" />
				<span class="v_middle">Force Delete</span>
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


//=========================================
//Server Home
//=========================================

$templates['server_home'] = '
	<div class="title">
		<img src="{site_url}/icons/32x32/devices/nfs_unmount.png" alt="Status" class="v_middle" />
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">{hostname} Server Home</a>
	</div>
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="40%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr class="even">
					<td width="30%">
						<div><strong>Uptime</strong></div>
						<div><small>Time server has been online.</small></div>
					</td>
					<td>{stats_uptime}</td>
				</tr>
				<tr class="odd">
					<td width="40%">
						<div><strong>Server IP Address</strong></div>
						<div><small>Server IP Address</small></div>
					</td>
					<td>
						<div>{ip}</div>
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Virtual Machines</strong></div>
						<div><small>Number of Virtual Machines assigned to server.</small></div>
					</td>
					<td>
						<div>{stats_vms}</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Server Load</strong></div>
						<div><small>Current resource load reported by the server.</small></div>
					</td>
					<td>
						<div>{stats_load}</div>
					</td>
				</tr>
				</table>
			</td>
			<td valign="top"  width="60%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr class="odd">
					<td width="40%">
						<div><strong>Memory Usage</strong></div>
						<div><small>Current memory usage.</small></div>
					</td>
					<td>
						<div>{stats_usage_mem}/{stats_total_mem}{stats_mem_den}</div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{stats_mem_pct}%"></td>
								<td width="{stats_mem_rep}%"></td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Disk Usage</strong></div>
						<div><small>Current usage of disk space allotted.</small></div>
					</td>
					<td>
						<div>{stats_usage_disk_space}/{stats_total_disk_space}{stats_disk_den}</div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{stats_disk_pct}%"><!--No Content--></td>
								<td width="{stats_disk_rep}%"></td>
							</tr>
							</table>
						</div>
					</tr>
				</tr>
				{cpu}
				<tr class="even">
					<td colspan="2" class="text_center">
						<div><strong>Allocated Resources</strong></div>
						<div><small>These are the resources allocated to VM\'s on this server.</small></div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Guaranteed Memory</strong></div>
					</td>
					<td>
						{stats_guar_mem}{stats_guar_mem_den}
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Burstable Memeory</strong></div>
					</td>
					<td>
						{stats_burst_mem}{stats_burst_mem_den}
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Disk Space</strong></div>
					</td>
					<td>
						{stats_udisk}{stats_udisk_den}
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Outgoing BW</strong></div>
					</td>
					<td>
						{stats_out_bw}{stats_out_bw_den}
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Incoming BW</strong></div>
					</td>
					<td>
						{stats_in_bw}{stats_in_bw_den}
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
	<div class="title">Server Operations</div>
	<div class="body">
		{server_operations}
		<div class="clear_both"><!--No Content--></div>
	</div>

	<div class="title">Server Actions</div>
	<div class="body">
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=reboot&server_id={server_id}" class="serverAction">
				<img src="icons/48x48/apps/restart.png" alt="Reboot" /><br />
				Reboot
			</a>
		</div>
		<!--<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=stop&server_id={server_id}" class="serverAction">
				<img src="icons/48x48/apps/error.png" alt="Shutdown" /><br />
				Shutdown
			</a>
		</div>-->
		<div class="clear_both"><!--No Content--></div>
	</div>
';

$templates['cpu_stat_row'] = '
	<tr class="{row}">
		<td>
			<div><strong>Processor(s)</strong></div>
			<div><small>{cpu MHz}mhz</small></div>
		</td>
		<td>{model name} x{cpu_count}</td>
	</tr>
';

$templates['server_operations'] = '
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=details&server_id={server_id}">
				<img src="icons/48x48/apps/advancedsettings.png" alt="Server Details" /><br />
				Server Details
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=ssh_port&server_id={server_id}">
				<img src="icons/48x48/devices/nfs_unmount.png" alt="Change SSH Port" /><br />
				Change SSH Port
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=root_password&server_id={server_id}">
				<img src="icons/48x48/apps/gpgsm.png" alt="Root Password" /><br />
				Change Root Password
			</a>
		</div>
		{change_hostname}
		{install_driver}
		{remove_driver}
		{setup_network}
		{setup_keys}
';

$templates['change_hostname_icon'] = '
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=hostname&server_id={server_id}">
				<img src="icons/48x48/apps/package_development.png" alt="Hostname" /><br />
				Change Hostname
			</a>
		</div>
';

$templates['install_driver_icon'] = '
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=install_driver&server_id={server_id}">
				<img src="icons/48x48/filesystems/folder_green.png" alt="Install Driver" /><br />
				Install/Activate Driver (BETA)
			</a>
		</div>
';

$templates['remove_driver_icon'] = '
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=remove_driver&server_id={server_id}">
				<img src="icons/48x48/filesystems/folder_red.png" alt="Remove Driver" /><br />
				Remove/Deactivate Driver (BETA)
			</a>
		</div>
';


$templates['setup_network_icon'] = '
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=setup_network&server_id={server_id}">
				<img src="icons/48x48/filesystems/Globe.png" alt="Setup Network" /><br />
				Setup/Reinitialize Network
			</a>
		</div>
';

$templates['setup_keys_icon'] = '
		<div class="feature_block">
			<a href="index.php?app=servers&sec=server_home&act=setup_keys&server_id={server_id}">
				<img src="icons/48x48/filesystems/link.png" alt="Setup Keys" /><br />
				Setup/Reinitialize Keys
			</a>
		</div>
';

$templates['hostname_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="change_hostname" />
	<div class="body">
		<fieldset>
			<legend>Change Hostname for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Hostname</strong></div>
					<div><small>Set hostname to a FQDN.</small></div>
				</td>
				<td>
					<input type="text" name="hostname" id="hostname" value="{hostname}" />
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Change Hostname" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

$templates['setup_keys_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="setup_keys" />
	<div class="body">
		<fieldset>
			<legend>Setup Keys for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Server Password</strong></div>
					<div><small>Root password of server. Used for installation and key generation. It is then discarded.</small></div>
				</td>
				<td><input type="text" name="password" value="{password}" size="40" autocomplete="off" /></td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Setup Keys" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

$templates['install_driver_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="install_driver" />
	<div class="body">
		<fieldset>
			<legend>Install Driver for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Driver</strong></div>
					<div><small>Select the driver to install. (This will deactivate and installed drivers.)</small></div>
				</td>
				<td>{drivers}</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Install Driver" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

$templates['remove_driver_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="remove_driver" />
	<div class="body">
		<fieldset>
			<legend>Remove Driver for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Driver</strong></div>
					<div><small>Select the driver to remove.</small></div>
				</td>
				<td>{drivers}</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Remove Driver" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

$templates['root_password_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="root_password" />
	<div class="body">
		<fieldset>
			<legend>Server Information for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Root Password</strong></div>
					<div><small>Set the root password for the Server.</small></div>
				</td>
				<td>
					<input type="password" name="root_password" id="root_password" />
				</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Confirm Root Password</strong></div>
					<div><small>Confirm the root password for the Server.</small></div>
				</td>
				<td>
					<input type="password" name="confirm_root_password" id="confirm_root_password" />
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Change Root Password" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

$templates['ssh_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="change_ssh" />
	<div class="body">
		<fieldset>
			<legend>Server Information for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>SSH Port</strong></div>
					<div><small>Set the SSH port to access the server.</small></div>
				</td>
				<td>
					<input type="text" name="port" id="port" value="{port}" />
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Change SSH Port" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

$templates['details_form'] = '
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="server_id" value="{server_id}" />
	<input type="hidden" name="action" value="details" />
	<div class="body">
		<fieldset>
			<legend>Server Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Server Name</strong></div>
					<div><small>Usually the hostname for reference. Ex: srv1.panenthe.com</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Datacenter</strong></div>
					<div><small>Location where the server is located.</small></div>
				</td>
				<td><input type="text" name="datacenter" value="{datacenter}" size="40" /.</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Server IP Address</strong></div>
					<div><small>IP address used to access the server. This can also be a domain name. Ex: srv1.panenthe.com</small>
				</td>
				<td><input type="text" name="ip" value="{ip}" size="40" /></td>
			</tr>
			<tr class="odd" style="display: none;">
				<td width="30%">
					<div><strong>Server Hostname</strong></div>
					<div><small>Hostname of the server. Ex: srv1.panenthe.com</small>
				</td>
				<td><input type="text" name="hostname" value="{hostname}" size="40" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Update Server" /></td>
			</tr>
			</table>
		</fieldset>
	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=servers&sec=server_home&server_id={server_id}">Back to Server Home</a>
	</div>
';

?>
