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

$templates['client_title'] = '	<div class="title">{title}</div>
';

$templates['form'] = '
	<form action="" method="post">
	<input type="hidden" name="user_id" value="{user_id}" />
	<div class="body">
		<fieldset>
			<legend>User Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
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
				<td>
					<input type="text" name="email" id="email" value="{email}" size="40" />
				</td>
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
			{staff_member}
			<tr>
				<td colspan="2"><input type="submit" value="Save user" /></td>
			</tr>
			</table>
		</fieldset>
	</div>
	</form>
';

$templates['staff_member'] = '
<tr class="even">
			<td width="30%">
				<div><strong>Staff Member?</strong></div>
				<div><small>Staff members can administrate the system, add VM\'s manage users etc.</small></div>
			</td>
			<td>
				<select name="is_staff">
					<option value="0" {is_not_staff}>No</option>
					<option value="1" {is_staff}>Yes</option>
				</select>
			</td>
		</tr>
';

$templates['client_staff_member'] = '
	<input type="hidden" name="is_staff" value="0" />
';

$templates['browse_top'] = '
	<div class="body">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr class="table_search">
			<form action="" method="get">
			<input type="hidden" name="app" value="users" />
			<input type="hidden" name="sec" value="{browse_sec}" />
			<td colspan="4">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="users" />
			<input type="hidden" name="sec" value="{browse_sec}" />
			<td class="text_right" width="300" colspan="4">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>User Id</td>
			<td>Username</td>
			<td>Name</td>
			<td>Staff</td>
			<td>Created</td>
			<td>Modified</td>
			<td>Options</td>
		</tr>
		<form action="" method="post" class="actionForm">
';

//Remember for Stable add {email} back in

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{user_id}" id="browse_action_{user_id}" class="browse_action" /></td>
			<td><label for="browse_action_{user_id}">{user_id}</label></td>
			<td>
				<label for="browse_action_{user_id}">
					<div><a href="index.php?app=users&sec=update_user&user_id={user_id}"><strong>{username}</strong></a></div>
					<div><small>{email}</small></div>
				</label>
			</td>
			<td><label for="browse_action_{user_id}">{first_name} {last_name}</label></td>
			<td><label for="browse_action_{user_id}">{is_staff}</label></td>
			<td><label for="browse_action_{user_id}">{created}</label></td>
			<td><label for="browse_action_{user_id}">{modified}</label></td>
			<td>
				<a href="index.php?app=users&sec={sec}&remove_user={user_id}" class="actionLink">Delete</a>
			</td>
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
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="browse_action_delete" value="Remove Users" />
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
