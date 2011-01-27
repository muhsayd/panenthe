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

$templates['title'] = '';

$templates['form_step1'] = '
	<form action="" method="post">
	<input type="hidden" name="create_vps_step1" value="true">
	<div class="body">
	
		<fieldset>
			<legend>Choose Server</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Server</strong></div>
				</td>
				<td>
					{server_id}
				</td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Configure VPS" /></td>
			</tr>
			</table>
		</fieldset>
		
	</div>
';

$templates['form'] = '
	<form action="" method="post">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="create_vps" />
	<div class="body">

		<fieldset>
			<legend>VM Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Name</strong></div>
					<div><small>The name used to reference the VM.</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Hostname</strong></div>
					<div><small>Operating system hostname.</small></div>
				</td>
				<td><input type="text" name="hostname" value="{hostname}" size="40" /></td>
			</tr>
			<tr class="even">
				<td wsave_vpsidth="30%">
					<div><strong>OS Template</strong></div>
					<div><small>Choose the Operating System that will be installed on the server.</small></div>
				</td>
				<td>
					{ost}
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Server</strong></div>
					<div><small>Select the server that the Virtual Machine will reside on.</small></div>
				</td>
				<td>
					<input type="hidden" name="server_id" value="{server_id}" />
					{server}
				</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Driver</strong></div>
					<div><small>Please select the virtualization software this VM will use.</small></div>
				</td>
				<td>
					<input type="hidden" name="driver_id" value="{driver_id}" />
					{driver}
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Root Password</strong></div>
					<div><small>Set the root password for the VM.</small></div>
				</td>
				<td>
					<input type="password" name="root_password" id="root_password" /> ({password_message})
				</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Confirm Root Password</strong></div>
					<div><small>Confirm the root password for the VM.</small></div>
				</td>
				<td>
					<input type="password" name="confirm_root_password" id="confirm_root_password" />  ({password_message})
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>VM Kernel</strong></div>
				</td>
				<td>
					{kernel}
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>User Information</legend>
			
			<div>
				<strong>User Assignment Type</strong>
				<select name="user_add_type" id="user_add_type">
					<option value="add_user" {add_user}>Create New User</option>
					<option value="assign_user" {assign_user}>Assign Existing User</option>
				</select>
			</div>

			<table cellpadding="0" cellspacing="0" width="100%" id="assign_user" style="display: none;">
			<tr>
				<td colspan="2">
					<div class="even">
						<div><strong>User to Assign</strong></div>
						<div><small>Select a User that will be able to manage this VPS.</small></div>
					</div>
					<div class="odd">
						<select name="user_id">
							{user_id}
						</select>
					</div>
				</td>
			</tr>
			</table>

			<table cellpadding="0" cellspacing="0" width="100%" id="add_user" style="display:none;">
			<tr class="even">
				<td width="30%">
					<div><strong>Username</strong></div>
					<div><small>The username should be alphanumeric with no spaces.</small></div>
					<div><small>Other allowed chars are . _ @</small></div>
					<div><small>Min Length: 4</small></div>
					<div><small>Max Length: 32</small></div>
				</td>
				<td><input type="text" name="username" value="{username}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Password</strong></div>
					<div><small>This is the password the user will use to access the panel. A strong password should be alphanumeric with a capital and a symbol.</small></div>
					<div><small>Min Length: 4</small></div>
					<div><small>Max Length: 32</small></div>
				</td>
				<td><input type="password" name="password" value="" size="20" /> ({password_message})</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Confirm Password</strong></div>
					<div><small>This must match the password first entered to confirm the password is correct.</small></div>
				</td>
				<td><input type="password" name="confirm_password" value="" size="20" /> ({password_message})</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Email</strong></div>
					<div><small>This should be a valid email address the user can receive system emails at.</small></div>
				</td>
				<td><input type="text" name="email" value="{email}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>First Name</strong></div>
					<div><small>The users first name. Used for addressing the user.</small></div>
				</td>
				<td><input type="text" name="first_name" value="{first_name}" size="20" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Last Name</strong></div>
					<div><small>The users last name. Used for addressing the user.</small></div>
				</td>
				<td><input type="text" name="last_name" value="{last_name}" size="20" /></td>
			</tr>
			</table>
			
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="30%">
					<strong>Send Welcome Email</strong>
				</td>
				<td>
					 <input type="checkbox" name="welcome_email" value="true" {welcome_email} />
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>IP Management</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>IP Addresses From Pool</strong></div>
					<div><small>As many available IPs will be added to the VPS from the pool. If there are not enough IPs the system will allocate all it can.</small></div>
				</td>
				<td>
					<input type="text" size="3" name="no_ips" value="{no_ips}" />
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Assign IP Addresses</strong></div>
					<div><small>Leave blank for auto assignment otherwise enter 1 IP per line for manual assignment. If ther are more IPs assigned than are input here the system will auto assign the rest.</small></div>
				</td>
				<td>
					<textarea name="manual_ips" style="width: 300px; height: 100px;">{manual_ips}</textarea>
				</td>
			</tr>
			</table>
		</fieldset>

		<script type="text/javascript">
			var plan_information = [];
			{plan_information}
		</script>

		<fieldset>
			<legend>Limits</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td>
					<div><strong>Select Resource Plan</strong></div>
					<div><small>Selecting a plan will auto fill all the below values.</small></div>
				</td>
				<td>
					{plan_id}
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Disk Space (MB)</strong></div>
					<div><small>Ex: 30000</small></div>
				</td>
				<td><input type="text" name="disk_space" value="{disk_space}" id="disk_space" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Backup Space (MB)</strong></div>
					<div><small>0 to disable. Ex: 15000</small></div>
				</td>
				<td><input type="text" name="backup_space" value="{backup_space}" id="backup_space" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Swap Space (MB)</strong></div>
					<div><small>Not supported by all drivers. Ex: 512</small></div>
				</td>
				<td><input type="text" name="swap_space" value="{swap_space}" id="swap_space" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Guaranteed Memory (MB)</strong></div>
					<div><small>Drivers that dont support burstable memory will use this value. Ex: 512</small></div>
				</td>
				<td><input type="text" name="g_mem" value="{g_mem}" id="g_mem" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Burstable Memory (MB)</strong></div>
					<div><small>Not supported by all drivers. Ex: 512</small></div>
				</td>
				<td><input type="text" name="b_mem" value="{b_mem}" id="b_mem" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>CPU Percentage</strong></div>
					<div><small>Percent of total CPU allowed. Ex: 75</small></div>
				</td>
				<td><input type="text" name="cpu_pct" value="{cpu_pct}" id="cpu_pct" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>CPU Multiplier</strong></div>
					<div><small>If the server has multiple cores, this will multiply the percentage to enable multiple core use. Ex: 2</small></div>
				</td>
				<td><input type="text" name="cpu_num" value="{cpu_num}" id="cpu_num" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Outgoing Traffic (GB)</strong></div>
					<div><small>Data downloaded from the server by visitors. Ex: 30</small></div>
				</td>
				<td><input type="text" name="out_bw" value="{out_bw}" id="out_bw" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Incoming Traffic (GB)</strong></div>
					<div><span style="color: red;">If left blank Outgoing Traffic will be used as a Full Duplex amount.</span></div>
					<div><small>Data download by the server from other srouces. Ex 15</div>
				</td>
				<td><input type="text" name="in_bw" value="{in_bw}" id="in_bw" size="40" /></td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Create VPS" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
';

$templates['confirmation'] = '
	<div class="body">

		<fieldset>
			<legend>VM Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Name</strong></div>
				</td>
				<td>{name}</td>
			</tr>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Hostname</strong></div>
				</td>
				<td>{hostname}</td>
			</tr>
			<tr class="even">
				<td wsave_vpsidth="30%">
					<div><strong>OS Template</strong></div>
				</td>
				<td>
					{ost}
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Root Password</strong></div>
				</td>
				<td>
					<strong>{root_password}</strong>
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>User Information</legend>

			<table cellpadding="0" cellspacing="0" width="100%" id="add_user">
			<tr class="even">
				<td width="30%">
					<div><strong>Username</strong></div>
				</td>
				<td>{username}</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Password</strong></div>
				</td>
				<td>{password}</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Email</strong></div>
				</td>
				<td>{email}</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>First Name</strong></div>
				</td>
				<td>{first_name}</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Last Name</strong></div>
				</td>
				<td>{last_name}</td>
			</tr>
			</table>
			
		</fieldset>

		<fieldset>
			<legend>IP Management</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>Main IP Address</strong></div>
				</td>
				<td>
					{ip_address}
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Limits</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Disk Space (MB)</strong></div>
				</td>
				<td>{disk_space}</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Backup Space (MB)</strong></div>
				</td>
				<td>{backup_space}</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Swap Space (MB)</strong></div>
				</td>
				<td>{swap_space}</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Guaranteed Memory (MB)</strong></div>
				</td>
				<td>{g_mem}</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Burstable Memory (MB)</strong></div>
				</td>
				<td>{b_mem}</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>CPU Percentage</strong></div>
				</td>
				<td>{cpu_pct}</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>CPU Multiplier</strong></div>
				</td>
				<td>{cpu_num}</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Outgoing Traffic (GB)</strong></div>
				</td>
				<td>{out_bw}</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Incoming Traffic (GB)</strong></div>
				</td>
				<td>{in_bw}</td>
			</tr>
			</table>
		</fieldset>

	</div>
';

$templates['plan_information_row'] = '

		plan_information[{plan_id}] = [];
		plan_information[{plan_id}]["disk_space"] = "{disk_space}";
		plan_information[{plan_id}]["backup_space"] = "{backup_space}";
		plan_information[{plan_id}]["swap_space"] = "{swap_space}";
		plan_information[{plan_id}]["g_mem"] = "{g_mem}";
		plan_information[{plan_id}]["b_mem"] = "{b_mem}";
		plan_information[{plan_id}]["cpu_pct"] = "{cpu_pct}";
		plan_information[{plan_id}]["cpu_num"] = "{cpu_num}";
		plan_information[{plan_id}]["out_bw"] = "{out_bw}";
		plan_information[{plan_id}]["in_bw"] = "{in_bw}";

';

$templates['browse_top'] = '
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_vps" />
			<td colspan="5">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_vps" />
			<td class="text_right" colspan="4" width="300">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>VM ID</td>
			<td>Type</td>
			<td>Name</td>
			<td>IP Address</td>
			<td>Server</td>
			<td width="13%">Disk</td>
			<td width="13%">Bandwith <small>(out)</small></td>
			<td>Created</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{vps_id}" id="browse_action_{vps_id}" class="browse_action" /></td>
			<td class="center">
				<div><img src="{site_url}/{sicon}" alt="Status" /></div>
				<div><label for="browse_action_{vps_id}">{vps_id}</label></div>
			</td>
			<td><img src="{site_url}/icons/drivers/{driver_ext_ref}.png" alt="{driver_name}" /></td>
			<td>
				<label for="browse_action_{vps_id}">
					<div>
						<strong>
							<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">{name}</a>
						</strong>
					</div>
					<div><small>{ost}</small></div>
				</label>
			</td>
			<td><label for="browse_action_{vps_id}">{ip_address}</label></td>
			<td><label for="browse_action_{vps_id}">{server}</label></td>
			<td>
				<label for="browse_action_{vps_id}">
					<div>{disk_usage}/{disk_space}{disk_den}</div>
					<div>
						<table cellpaddig="0" cellspacing="0" class="stats_table">
						<tr>
							<td class="used" width="{disk_pct}%"></td>
							<td width="{disk_rep}%"></td>
						</tr>
						</table>
					</div>
				</label>
			</td>
			<td>
				<label for="browse_action_{vps_id}">
					<div>{out_bw_usage}/{out_bw}{out_bw_den}</div>
					<div>
						<table cellpaddig="0" cellspacing="0" class="stats_table">
						<tr>
							<td class="used" width="{out_bw_pct}%"></td>
							<td width="{out_bw_rep}%"></td>
						</tr>
						</table>
					</div>
				</label>
			</td>
			<td><label for="browse_action_{vps_id}">{created}</label></td>
		</tr>
';

$templates['staff_row_actions'] = '
	<a href="index.php?app=vps&sec=remove_vps&remove_vps={vps_id}" class="actionLink">Delete</a>
';

$templates['client_row_actions'] = '
	None
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="10"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<tr class="table_search">
			<td colspan="5">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" class="v_middle" /> 
				<input type="submit" name="browse_action_delete" value="Remove VPS" class="v_middle" />
				<input type="checkbox" name="force_delete" value="true" id="force_delete" class="v_middle" />
				<span class="v_middle">Force Delete</span>
			</td>
			<td class="text_right" colspan="4">
				{pagination_html}
			</td>
		</tr>
		</form>
		</table>
		<div class="body">
			<img src="icons/16x16/actions/adept_commit.png" alt="Running" class="v_middle" />
			<span class="v_middle">= Running</span>
			<img src="icons/16x16/actions/messagebox_critical.png" alt="Stopped" class="v_middle" />
			<span class="v_middle">= Stopped</span>
			<img src="icons/16x16/actions/button_cancel.png" alt="Deleted" class="v_middle" />
			<span class="v_middle">= Deleted</span>
			<img src="icons/16x16/actions/messagebox_info.png" alt="None" class="v_middle" />
			<span class="v_middle">= None</span>
			<img src="icons/16x16/apps/important.png" atl="Suspended" class="v_middle" />
			<span class="v_middle">= Suspended</span>
		</div>
		<div>
			<strong>Note:</strong> Reported status is the last tracked status. 
			For live VM status click <a href="index.php?app=vps&sec=status_vps">
			here</a>.
		</div>
	</div>
';

$templates['client_browse_bot'] = '
		<tr class="table_search">
			<td colspan="5">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" />
			</td>
			<td class="text_right" colspan="4">
				{pagination_html}
			</td>
		</tr>
		</form>
		</table>
		<div class="body">
			<img src="icons/16x16/actions/adept_commit.png" alt="Running" class="v_middle" />
			<span class="v_middle">= Running</span>
			<img src="icons/16x16/actions/messagebox_critical.png" alt="Stopped" class="v_middle" />
			<span class="v_middle">= Stopped</span>
			<img src="icons/16x16/actions/button_cancel.png" alt="Deleted" class="v_middle" />
			<span class="v_middle">= Deleted</span>
			<img src="icons/16x16/actions/messagebox_info.png" alt="None" class="v_middle" />
			<span class="v_middle">= None</span>
			<img src="icons/16x16/apps/important.png" atl="Suspended" class="v_middle" />
			<span class="v_middle">= Suspended</span>
		</div>
	</div>
';

$templates['browse_search_select_row'] = '
		<option value="{col}"{selected}>{ver}</option>
';

$templates['browse_items_page_select_row'] = '
		<option value="{col}"{selected}>{ver}</option>
';

//=========================================
//Satus VPS 
//=========================================			
$templates['browse_top_status'] = '
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_vps" />
			<td colspan="4">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_vps" />
			<td class="text_right" colspan="2" width="300">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td width="10%"></td>
			<td width="10%">VM ID</td>
			<td>Type</td>
			<td>Name</td>
			<td>IP Address</td>
			<td>Memory</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_row_status'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{vps_id}" id="browse_action_{vps_id}" class="browse_action" /></td>
			<td width="10%">
				<div><img src="{site_url}/{sicon}" alt="Status" /></div>
				<div><label for="browse_action_{vps_id}">{vps_id}</label></div>
			</td>
			<td width="10%">
				<img src="{site_url}/icons/drivers/{driver_ext_ref}.png" alt="{driver_name}" />
			</td>
			<td>
				<label for="browse_action_{vps_id}">
					<div>
						<strong>
							<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">{name}</a>
						</strong>
					</div>
					<div><small>{ost}</small></div>
				</label>
			</td>
			<td width="15%"><label for="browse_action_{vps_id}">{ip_address}</label></td>
			<td width="15%">
				<label for="browse_action_{vps_id}">
					<div>{memory_usage}/{g_mem}{memory_den}</div>
					<div>
						<table cellpaddig="0" cellspacing="0" class="stats_table">
						<tr>
							<td class="used" width="{memory_pct}%"></td>
							<td width="{memory_rep}%"></td>
						</tr>
						</table>
					</div>
				</label>
			</td>
		</tr>
';

$templates['browse_row_none_status'] = '
		<tr>
			<td colspan="5"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot_status'] = '
		<tr class="table_search">
			<td colspan="4">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" />
			</td>
			<td class="text_right" colspan="2">
				{pagination_html}
			</td>
		</tr>
		</form>
		</table>
		<div class="body">
			<img src="icons/16x16/actions/adept_commit.png" alt="Running" class="v_middle" />
			<span class="v_middle">= Running</span>
			<img src="icons/16x16/actions/messagebox_critical.png" alt="Stopped" class="v_middle" />
			<span class="v_middle">= Stopped</span>
			<img src="icons/16x16/actions/button_cancel.png" alt="Deleted" class="v_middle" />
			<span class="v_middle">= Deleted</span>
			<img src="icons/16x16/actions/messagebox_info.png" alt="None" class="v_middle" />
			<span class="v_middle">= None</span>
		</div>
	</div>
';

//=========================================
//VPS HOME
//=========================================

$templates['suspend_msg'] = '
	<div class="suspend_msg">
		{suspend_msg}
	</div>
';

$templates['vps_home'] = '
	{suspend_msg}
	<div class="title">
		<img src="{site_url}/icons/drivers/{driver_ext_ref}.png" alt="{driver_name}" style="vertical-align: middle;" />
		<img src="{site_url}/{sicon}" alt="{status}" class="v_middle" />
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">
			{name} Virtual Machine Home
		</a>
	</div>
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" width="50%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr class="even">
					<td width="30%">
						<div><strong>Hostname</strong></div>
						<div><small>Operating System Hostname</small></div>
					</td>
					<td>
						<div>{hostname}</div>
						<div><strong>Created:</strong> {created}</div>
					</td>
				</tr>
				<tr class="odd">
					<td width="40%">
						<div><strong>Main IP Address</strong></div>
						<div><small>VM\'s main IP address for access.</small></div>
					</td>
					<td>
						<div>{main_ipaddress}</div>
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>VPS ID</strong></div>
						<div><small>System VPS ID assigned by Panenthe</small></div>
					</td>
					<td>
						<div>{vps_id}</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Real ID</strong></div>
						<div><small>VPS ID assigned by the driver.</small></div>
					</td>
					<td>
						<div>{real_id}</div>
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Operating System</strong></div>
					</td>
					<td>
						<div>{ost}</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Server</strong></div>
					</td>
					<td>
						<div>{server_name} ({driver_name})</div>
					</td>
				</tr>
				</table>
			</td>
			<td valign="top"  width="50%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr class="odd">
					<td width="40%">
						<div><strong>Memory Usage</strong></div>
						<div><small>Current memory usage.</small></div>
					</td>
					<td>
						<div>{memory_usage}/{g_mem}{memory_den}</div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{memory_pct}%"></td>
								<td width="{memory_rep}%"></td>
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
						<div>{disk_usage}/{disk_space}{disk_den}</div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{disk_pct}%"><!--No Content--></td>
								<td width="{disk_rep}%"></td>
							</tr>
							</table>
						</div>
					</tr>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Backup Usage</strong></div>
						<div><small>Usage of allotted backup space.</small></div>
					</td>
					<td>
						<div>{backup_usage}/{backup_space}{backup_den}</div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{backup_pct}%"></td>
								<td width="{backup_rep}%"></td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Bandwidth Usage</strong></div>
						<div><small>Month to date traffic usage.</small></div>
					</td>
					<td>
						<div>{out_bw_usage}/{out_bw}{out_bw_den} <small>(out)</small></div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{out_bw_pct}%"></td>
								<td width="{out_bw_rep}%"></td>
							</tr>
							</table>
						</div>
						<div>{in_bw_usage}/{in_bw}{in_bw_den} <small>(in)</small></div>
						<div>
							<table cellpaddig="0" cellspacing="0" class="stats_table">
							<tr>
								<td class="used" width="{in_bw_pct}%"></td>
								<td width="{in_bw_rep}%"></td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr class="odd">
					<td>
						<div><strong>Load Average</stronmg>
					</td>
					<td>
						<div>{load_average_1}, {load_average_5}, {load_average_15}</div>
					</td>
				</tr>
				<tr class="even">
					<td>
						<div><strong>Uptime</strong></div>
					</td>
					<td>
						<div>{uptime}</div>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
	<div class="title">VM Operations</div>
	<div class="body">
		{vm_operations}
		<div class="clear_both"><!--No Content--></div>
	</div>

	<div class="title">VM Actions</div>
	<div class="body">
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=start&vps_id={vps_id}">
				<img src="icons/48x48/apps/kterm.png" alt="Boot" /><br />
				Boot
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=reboot&vps_id={vps_id}">
				<img src="icons/48x48/apps/restart.png" alt="Reboot" /><br />
				Reboot
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=stop&vps_id={vps_id}">
				<img src="icons/48x48/apps/error.png" alt="Stop" /><br />
				Stop
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=poweroff&vps_id={vps_id}">
				<img src="icons/48x48/apps/logout.png" alt="Power Off" /><br />
				Power Off <br /><small>(Uses ACPI Shutdown)</small>
			</a>
		</div>
		<div class="clear_both"><!--No Content--></div>
	</div>
';

$templates['client_suspended'] = '
	<div class="title">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">{name} Virtual Machine Home</a>
	</div>
	<div class="body">
		The VPS you are attempting to access has been suspended. Please contact your provider to 
		get this issue resolved.
	</div>
';

$templates['staff_vm_operations'] = '
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=limits&vps_id={vps_id}">
				<img src="icons/48x48/apps/advancedsettings.png" alt="Change Limits" /><br />
				Change Limits
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=name&vps_id={vps_id}">
				<img src="icons/48x48/apps/package_development.png" alt="Change Name/Hostname" /><br />
				Change Name/Hostname
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=rebuild&vps_id={vps_id}">
				<img src="icons/48x48/devices/nfs_unmount.png" alt="Rebuild VM" /><br />
				Rebuild VM
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=root_password&vps_id={vps_id}">
				<img src="icons/48x48/apps/gpgsm.png" alt="Root Password" /><br />
				Root Password
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=browse_ips&vps_id={vps_id}">
				<img src="icons/48x48/apps/web.png" alt="Manage IP Addresses" /><br />
				IP Addresses
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=browse_au&vps_id={vps_id}">
				<img src="icons/48x48/apps/kuser.png" alt="Manage Assigned Users" /><br />
				Assigned Users
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=user_beancounts&vps_id={vps_id}">
				<img src="icons/48x48/apps/kfm.png" alt="User BeanCounts" /><br />
				User BeanCounts
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=suspension&vps_id={vps_id}">
				<img src="icons/48x48/apps/important.png" alt="{suspend_txt}" /><br />
				{suspend_txt}
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=create_ost&vps_id={vps_id}">
				<img src="icons/48x48/apps/package.png" alt="Create OS Template" /><br />
				Create OS Template
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=resend_welcome_email&vps_id={vps_id}">
				<img src="icons/48x48/other/mailappt.png" alt="Resend Welcome Email" /><br />
				Resend Weclome Email
			</a>
		</div>
';

$templates['client_vm_operations'] = '
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=rebuild&vps_id={vps_id}">
				<img src="icons/48x48/devices/nfs_unmount.png" alt="Rebuild VM" /><br />
				Rebuild VM
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=name&vps_id={vps_id}">
				<img src="icons/48x48/apps/package_development.png" alt="Change Name/Hostname" /><br />
				Change Name/Hostname
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=root_password&vps_id={vps_id}">
				<img src="icons/48x48/apps/gpgsm.png" alt="Root Password" /><br />
				Root Password
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=browse_ips&vps_id={vps_id}">
				<img src="icons/48x48/apps/web.png" alt="Manage IP Addresses" /><br />
				IP Addresses
			</a>
		</div>
		<div class="feature_block">
			<a href="index.php?app=vps&sec=vps_home&act=user_beancounts&vps_id={vps_id}">
				<img src="icons/48x48/apps/kfm.png" alt="User BeanCounts" /><br />
				User BeanCounts
			</a>
		</div>
';

$templates['limits_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	
	<script type="text/javascript">
		var plan_information = [];
		{plan_information}
	</script>

	<form action="" method="post">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="change_limits" />
	<div class="body">
		<fieldset>
			<legend>VM Limits for {vps_name}</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td>
					<div><strong>Select Resource Plan</strong></div>
					<div><small>Selecting a plan will auto fill all the below values.</small></div>
				</td>
				<td>
					{plan_id}
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Disk Space (MB)</strong></div>
					<div><small>Ex: 30000</small></div>
				</td>
				<td><input type="text" name="disk_space" value="{disk_space}" id="disk_space" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Backup Space (MB)</strong></div>
					<div><small>0 to disable. Ex: 15000</small></div>
				</td>
				<td><input type="text" name="backup_space" value="{backup_space}" id="backup_space" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Swap Space (MB)</strong></div>
					<div><small>Not supported by all drivers. Ex: 512</small></div>
				</td>
				<td><input type="text" name="swap_space" value="{swap_space}" id="swap_space" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Guaranteed Memory (MB)</strong></div>
					<div><small>Drivers that dont support burstable memory will use this value. Ex: 512</small></div>
				</td>
				<td><input type="text" name="g_mem" value="{g_mem}" id="g_mem" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Burstable Memory (MB)</strong></div>
					<div><small>Not supported by all drivers. Ex: 512</small></div>
				</td>
				<td><input type="text" name="b_mem" value="{b_mem}" id="b_mem" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>CPU Percentage</strong></div>
					<div><small>Percent of total CPU allowed. Ex: 75</small></div>
				</td>
				<td><input type="text" name="cpu_pct" value="{cpu_pct}" id="cpu_pct" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>CPU Multiplier</strong></div>
					<div><small>If the server has multiple cores, this will multiply the percentage to enable multiple core use. Ex: 2</small></div>
				</td>
				<td><input type="text" name="cpu_num" value="{cpu_num}" id="cpu_num" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Outgoing Traffic (GB)</strong></div>
					<div><small>Data downloaded from the server by visitors. Ex: 30</small></div>
				</td>
				<td><input type="text" name="out_bw" value="{out_bw}" id="out_bw" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Incoming Traffic (GB)</strong></div>
					<div><small>Data downloaded by the server. Ex: 30</small></div>
				</td>
				<td><input type="text" name="in_bw" value="{in_bw}" id="in_bw" size="40" /></td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Change Limits" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['create_ost_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="action" value="create_ost" />
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="arch" value="{arch}" />
	<input type="hidden" name="driver_id" value="{driver_id}" />
	<div class="body">
		<fieldset>
			<legend>Create OS Template from VM {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Name</strong></div>
					<div><small>The name is used to reference the OS Template in the interface.</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" readonly="readonly" /> - <input type="text" name="name_ext" value="" size="20" /> <small>ex: {name_ext}</small></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Architecture</strong></div>
					<div><small>Select the template architecture.</small></div>
				</td>
				<td>
					{arch}
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Create OS Template" /></td>
			</tr>
			</table>
		</fieldset>
	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['rebuild_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	
	<script type="text/javascript">
		window.addEvent("domready", function(){

			$("rebuild_form").addEvent("submit", function(e){
				var event = new Event(e);
				if(!$("confirm_rebuild").checked){
					event.stop();
					alert("You must confirm the rebuild!");
					loading_link.remove_loading();
				}
			});

		});
	</script>

	<form action="" method="post" id="rebuild_form">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="rebuild" />
	<div class="body">
		<fieldset>
			<legend>VM Information for {vps_name}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Hostname</strong></div>
					<div><small>Server hostname to reference it by.</small></div>
				</td>
				<td><input type="text" name="hostname" value="{hostname}" size="40" /></td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>OS Template</strong></div>
					<div><small>Choose the Operating System that will be installed on the server.</small></div>
				</td>
				<td>
					{ost}
				</td>
			</tr>
			<tr class="odd">
				<td>
					<div><strong>Root Password</strong></div>
				</td>
				<td>
					<input type="password" name="root_password" />
				</td>
			<tr>
			<tr class="even">
				<td>
					<div><strong>Confirm Password</strong></div>
				</td>
				<td>
					<input type="password" name="confirm_root_password" />
				</td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>
			
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2">
					<input type="checkbox" name="confirm_rebuild" id="confirm_rebuild" value="true" />
					<strong>Please Confirm</strong>
					<span style="color: red;">Rebuilding the VM will destroy all data on the machine.</span>
				</td>
			</tr
			<tr>
				<td colspan="2"><input type="submit" value="Rebuild VM" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['name_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>

	<form action="" method="post" id="rebuild_form">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="name" />
	<div class="body">
		<fieldset>
			<legend>VM Information for {vps_name}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>VM Name</strong></div>
					<div><small>Change the name of the VM.</small></div>
				</td>
				<td><input type="text" name="name" value="{name}" size="40" /></td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Hostname</strong></div>
					<div><small>Server hostname to reference it by.</small></div>
				</td>
				<td><input type="text" name="hostname" value="{hostname}" size="40" /></td>
			</tr>
			</table>
		</fieldset>

		<fieldset>
			<legend>Actions</legend>
			<input type="submit" value="Update VM" />
		</fieldset>

	</div>
	</form>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['root_password_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	
	<form action="" method="post">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="root_password" />
	<div class="body">
		<fieldset>
			<legend>VM Information for {hostname}</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Root Password</strong></div>
					<div><small>Set the root password for the VM.</small></div>
				</td>
				<td>
					<input type="password" name="root_password" id="root_password" /> ({password_message})
				</td>
			</tr>
			<tr class="even">
				<td width="30%">
					<div><strong>Confirm Root Password</strong></div>
					<div><small>Confirm the root password for the VM.</small></div>
				</td>
				<td>
					<input type="password" name="confirm_root_password" id="confirm_root_password" />  ({password_message})
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
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

//==============================
//User Beancount
//==============================

$templates['user_beancounts_top'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	<div class="title">User BeanCounts for {hostname}</div>
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_desc">
			<td>Resource</td>
			<td>Held</td>
			<td>Max Held</td>
			<td>Barrier</td>
			<td>Limit</td>
			<td>Fail Count</td>
		</tr>
';

$templates['user_beancounts_row'] = '
		<tr class="{row}">
			<td><strong>{resource}</strong></td>
			<td>{held}</td>
			<td>{maxheld}</td>
			<td>{barrier}</td>
			<td>{limit}</td>
			<td>{failcnt}</td>
		</tr>
';

$templates['user_beancounts_bot'] = '
		</table>
	</div>
';

//============================
//Manage IP Addresses
//============================

$templates['add_ip_address_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=browse_ips&vps_id={vps_id}">Back to VPS IP Address Manager</a>
	</div>
	<form action="" method="post">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="add_ips" />
	<div class="body">
		<fieldset>
			<legend>IP Management for {hostname}</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="even">
				<td width="30%">
					<div><strong>IP Addresses From Pool</strong></div>
					<div><small>As many available IPs will be added to the VPS from the pool. If there are not enough IPs the system will allocate all it can.</small></div>
				</td>
				<td>
					<input type="text" size="3" name="no_ips" value="{no_ips}" />
				</td>
			</tr>
			<tr class="odd">
				<td width="30%">
					<div><strong>Assign IP Addresses</strong></div>
					<div><small>Leave blank for auto assignment otherwise enter 1 IP per line for manual assignment. If ther are more IPs assigned than are input here the system will auto assign the rest.</small></div>
				</td>
				<td>
					<textarea name="manual_ips" style="width: 300px; height: 100px;">{manual_ips}</textarea>
				</td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Add IPs" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	
';

$templates['browse_ips_top'] = '
	<div class="title">Manage IP Addresses for {hostname}</div>
	{ip_actions}
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="vps_id" value="{vps_id}">
			<input type="hidden" name="sec" value="browse_ips" />
			<td colspan="3">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_ips" />
			<td class="text_right" width="300" colspan="2">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>VM ID</td>
			<td>Hostname</td>
			<td>IP Address</td>
			<td>IP Options</td>
		</tr>
		<form action="index.php?app=vps&sec=browse_ips&vps_id={vps_id}" method="post" class="actionForm">
';

$templates['staff_ip_actions'] = '
	<div class="body">
		<div class="feature_block">
			<a href="index.php?app=vps&sec=add_ips&vps_id={vps_id}">
				<img src="icons/48x48/apps/web.png" alt="Add IP Addresses" /><br />
				Add IP Addresses
			</a>
		</div>
	</div>
';

$templates['client_ip_actions'] = '';

$templates['browse_ips_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{ip_id}" id="browse_action_{ip_id}" class="browse_action" /></td>
			<td><label for="browse_action_{ip_id}">{vps_id}</label></td>
			<td><label for="browse_action_{ip_id}">{hostname}</label></td>
			<td><label for="browse_action_{ip_id}">{ip_addr}</label></td>
			<td>
				{row_actions}
			</td>
		</tr>
';

$templates['staff_ips_row_actions'] = '
	<a href="index.php?app=vps&sec=browse_ips&remove_ip={ip_id}&vps_id={vps_id}" class="actionLink">Delete</a>
';

$templates['client_ips_row_actions'] = '
	None
';

$templates['browse_ips_row_none'] = '
		<tr>
			<td colspan="5"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['staff_browse_ips_bot'] = '
		<tr class="table_search">
			<td colspan="3">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove IPs" />
			</td>
			<td class="text_right" colspan="2">
				{pagination_html}
			</td>
		</tr>
		</form>
		</table>
	</div>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['client_browse_ips_bot'] = '
		<tr class="table_search">
			<td colspan="3">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" />
			</td>
			<td class="text_right" colspan="2">
				{pagination_html}
			</td>
		</tr>
		</form>
		</table>
	</div>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['ips_title'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

//===============================
//Assigned Users
//===============================

$templates['add_au_form'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=browse_au&vps_id={vps_id}">Back to VPS Assigned Users</a>
	</div>
	<form action="" method="post">
	<input type="hidden" name="vps_id" value="{vps_id}" />
	<input type="hidden" name="action" value="add_au" />
	<div class="body">
		<fieldset>
			<legend>VPS Users Assignment for {hostname}</legend>

			<div class="even">
				<div><strong>User to Assign</strong></div>
				<div><small>Select a User that will be able to manage this VPS.</small></div>
			</div>
			<div class="odd">
				<select name="user_id">					{users}
				</select>
			</div>
				
		</fieldset>
		
		<fieldset>
			<legend>Actions</legend>

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><input type="submit" value="Assign User" /></td>
			</tr>
			</table>
		</fieldset>

	</div>
	</form>
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	
	
';

$templates['browse_au_top'] = '
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
	<div class="title">Browse VM Assigned Users for {hostname}</div>
	<div class="body">
		<div class="feature_block">
			<a href="index.php?app=vps&sec=add_au&vps_id={vps_id}">
				<img src="icons/48x48/apps/kuser.png" alt="Assign User" /><br />
				Assign User
			</a>
		</div>
	</div>
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_au" />
			<td colspan="4">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="vps" />
			<input type="hidden" name="sec" value="browse_au" />
			<td class="text_right" width="300" colspan="3">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>User ID</td>
			<td>Username</td>
			<td>Email</td>
			<td>First Name</td>
			<td>Last Name</td>
			<td>User Options</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

$templates['browse_au_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{user_id}" id="browse_action_{user_id}" class="browse_action" /></td>
			<td><label for="browse_action_{user_id}">{user_id}</label></td>
			<td><label for="browse_action_{user_id}">{username}</label></td>
			<td><label for="browse_action_{user_id}">{email}</label></td>
			<td><label for="browse_action_{user_id}">{first_name}</label></td>
			<td><label for="browse_action_{user_id}">{last_name}</label></td>
			<td>
				<a href="index.php?app=vps&sec=browse_au&remove_au={user_id}&vps_id={vps_id}" class="actionLink">Delete</a>
			</td>
		</tr>
';

$templates['browse_au_row_none'] = '
		<tr>
			<td colspan="5"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_au_bot'] = '
		<tr class="table_search">
			<td colspan="6">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove Users" />
			</td>
			<td class="text_right">
				{pagination_html}
			</td>
		</tr>
		</form>
		</table>
	</div>
	
	<div class="body">
		<a href="index.php?app=vps&sec=vps_home&vps_id={vps_id}">Back to VPS Home</a>
	</div>
';

$templates['au_title'] = '
';

$templates['user_au_add_row'] = '
	<option value="{user_id}"{selected}> 
		ID#: {user_id} &nbsp;&nbsp;&nbsp; 
		Username: {username} &nbsp;&nbsp;&nbsp;
		Email: {email} &nbsp;&nbsp;&nbsp;
	</option>
';

?>
