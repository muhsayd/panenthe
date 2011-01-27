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
	<script type="text/javascript">
		window.addEvent("domready", function(){
			$("event_form").addEvent("submit", function(event){
				var e = new Event(event);
				if($("message").value == ""){
					e.stop();
					alert("Message cannot be left blank.");
					loading_link.remove_loading();
					return;
				}
			});
		});
	</script>
	<form action="" method="post" id="event_form">
	<input type="hidden" name="event_id" value="{event_id}" />
	<div class="body">
		<fieldset>
			<legend>Event Information</legend>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr class="odd">
				<td width="30%">
					<div><strong>Message</strong></div>
					<div><small>This is the message that will be displayed to describe the event.</small></div>
				</td>
				<td><input type="text" name="message" id="message" value="{message}" size="40" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Save event" /></td>
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
			<input type="hidden" name="app" value="events" />
			<input type="hidden" name="sec" value="{browse_sec}" />
			<td colspan="3">
					<select name="search_col">{search_columns}</select>
					<input type="text" name="search_words" value="{search_words}" />
					<input type="submit" value="Search" />
			</td>
			</form>
			<form action="" method="get" id="items_per_page">
			<input type="hidden" name="app" value="events" />
			<input type="hidden" name="sec" value="{browse_sec}" />
			<td class="text_right" colspan="1" width="400">
				Display <select name="items_per_page" class="items_per_page">{items_per_page}</select> items per page.
				{pagination_html}
			</td>
			</form>
		</tr>

		<tr class="table_desc">
			<td></td>
			<td>ID</td>
			<td>Time</td>
			<td>Message</td>
		</tr>
		<form action="" method="post">
';

$templates['browse_row'] = '
		<tr class="browse_row{row}">
			<td><input type="checkbox" name="browse_action[]" value="{event_id}" id="browse_action_{event_id}" class="browse_action" /></td>
			<td><label for="browse_action_{event_id}">{event_id}</label></td>
			<td><label for="browse_action_{event_id}">{time}</label></td>
			<td><label for="browse_action_{event_id}">{message}</label></td>
			<!--<td>
				<a href="index.php?app=events&sec={row_action_type}&{row_action_type}={event_id}">{row_action}</a>
			</td>-->
		</tr>
';

$templates['browse_row_none'] = '
		<tr>
			<td colspan="4"><em>There are no rows to display.</em></td>
		</tr>
';

$templates['browse_bot'] = '
		<tr class="table_search">
			<td colspan="3">
				<input type="checkbox" name="browse_check_all" id="browse_check_all" /> <input type="submit" name="{browse_action_type}" value="{browse_action}" />
			</td>
			<td class="text_right" colspan="1">
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
