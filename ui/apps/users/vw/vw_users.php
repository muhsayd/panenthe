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

class vw_users {

	public static function title($title){

		if(main::$is_staff){
			dev::$tpl->parse(
				'users',
				'title',
				array(
					"title"	=>	$title
				)
			);
		}
		else
		{
			dev::$tpl->parse(
				'users',
				'client_title',
				array(
					"title"	=>	$title
				)
			);
		}
	}

	public static function form($fields,$add=false){

		if($add){
			$fields['password_message'] = 'Leave blank to generate.';
			$fields['is_staff'] = '';
			$fields['is_not_staff'] = '';
		}
		else
		{
			$fields['password_message'] = 'Leave blank for no change.';
		}
		
		if(main::$is_staff){
			$fields['staff_member'] = dev::$tpl->parse(
				'users',
				'staff_member',
				$fields,
				true
			);
		}
		else
		{
			$fields['staff_member'] = dev::$tpl->parse(
				'users',
				'client_staff_member',
				$fields,
				true
			);
		}
		
		$fields['random_email'] = time().'_'.mt_rand(0,2134).'@panenthe.com';
		
		dev::$tpl->parse(
			'users',
			'form',
			$fields
		);
	}

	public static function load_browse_js(){

		dev::$js->add_library('apps/users/js/users_browse.js');
	}

	public static function browse($items,$pagination_html,$sec){

		if(isset(dev::$get['search_words'])){
			$search_col = dev::$get['search_col'];
			$search_words = dev::$get['search_words'];
		}
		else
		{
			$search_col = '';
			$search_words = '';
		}

		if(isset(dev::$get['items_per_page'])){
			$items_col = dev::$get['items_per_page'];
		}
		else
		{
			$items_col = '10';
		}

		dev::$tpl->parse(
			'users',
			'browse_top',
			array(
				"browse_sec"		=>	$sec,
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_users::items_per_page($items_col)
			)
		);

		$i = 0;
		if(is_array($items) && count($items) > 0){
			foreach($items AS $item){

				if($i % 2 == 0){
					$row = ' odd';
				}
				else
				{
					$row = '';
				}

				if($item->get_is_staff() == '1'){
					$staff = 'Yes';
				}
				else
				{
					$staff = "No";
				}

				dev::$tpl->parse(
					'users',
					'browse_row',
					array(
							"user_id"			=>		$item->get_user_id(),
							"username"			=>		$item->get_username(),
							"password"			=>		$item->get_password(),
							"email"				=>		$item->get_email(),
							"first_name"			=>		$item->get_first_name(),
							"last_name"			=>		$item->get_last_name(),
							"is_staff"			=>		$staff,
							"sec"				=>		$sec,
							"created"			=>		date('m/d/Y',$item->get_created()),
							"modified"			=>		date('m/d/Y',$item->get_modified()),
						"row"			=>	$row
					)
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'users',
				'browse_row_none'
			);
		}

		dev::$tpl->parse(
			'users',
			'browse_bot',
			array(
				"pagination_html"	=>	$pagination_html
			)
		);
	}

	private static function get_search_columns($sel_column=""){

		$columns = array(
				"user_id"			=>		"User Id",
				"username"			=>		"Username",
				"password"			=>		"Password",
				"salt"			=>		"Salt",
				"email"			=>		"Email",
				"first_name"			=>		"First Name",
				"last_name"			=>		"Last Name",
				"is_staff"			=>		"Is Staff",
				"created"			=>		"Created",
				"modified"			=>		"Modified"
		);

		$search_columns = '';
		foreach($columns AS $col => $ver){
			if($sel_column == $col){
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}
			$search_columns .= dev::$tpl->parse(
				'users',
				'browse_search_select_row',
				array(
					"col"		=>	$col,
					"selected"	=>	$selected,
					"ver"		=>	$ver
				),
				true
			);
		}

		return $search_columns;
	}
	public static function items_per_page($sel_items=""){

		$items = array(
			"10"	=>	"10",
			"20"	=>	"20",
			"50"	=>	"50",
			"100"	=>	"100",
			"0"		=>	"All"
		);

		$items_per_page = '';
		foreach($items AS $col => $ver){
			if($sel_items == $col){
				$selected = ' selected="selected"';
			}
			else
			{
				$selected = '';
			}
			$items_per_page .= dev::$tpl->parse(
				'users',
				'browse_items_page_select_row',
				array(
					"col"		=>	$col,
					"selected"	=>	$selected,
					"ver"		=>	$ver
				),
				true
			);
		}

		return $items_per_page;
	}

}

?>
