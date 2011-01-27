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

class vw_ips {

	public static function title($title){

		dev::$tpl->parse(
			'vps',
			'ips_title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){

		dev::$tpl->parse(
			'vps',
			'add_ip_address_form',
			$fields
		);
		
	}

	public static function load_browse_js(){

		dev::$js->add_library('apps/vps/js/vps_browse.js');
	}

	public static function browse($items,$pagination_html){

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
		
		if(main::$is_staff){
			$ip_actions = dev::$tpl->parse(
				'vps',
				'staff_ip_actions',
				array(),
				true
			);
		}
		else
		{
			$ip_actions = dev::$tpl->parse(
				'vps',
				'client_ip_actions',
				array(),
				true
			);
		}

		dev::$tpl->parse(
			'vps',
			'browse_ips_top',
			array(
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_vps::items_per_page($items_col),
				"vps_id"			=>	dev::$get['vps_id'],
				"ip_actions"		=>	$ip_actions
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
				
				if(main::$is_staff){
					$row_actions = dev::$tpl->parse(
						'vps',
						'staff_ips_row_actions',
						$item,
						true
					);
				}
				else
				{
					$row_actions = dev::$tpl->parse(
						'vps',
						'client_ips_row_actions',
						$item,
						true
					);
				}

				dev::$tpl->parse(
					'vps',
					'browse_ips_row',
					array(
							"vps_id"			=>		$item['vps_id'],
							"hostname"			=>		$item['hostname'],
							"real_id"			=>		$item['real_id'],
							"ip_id"				=>		$item['ip_id'],
							"ip_addr"			=>		$item['ip_addr'],
							"row_actions"		=>		$row_actions,
							"row"				=>		$row
					)
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'vps',
				'browse_ips_row_none'
			);
		}
		
		if(main::$is_staff){
			dev::$tpl->parse(
				'vps',
				'staff_browse_ips_bot',
				array(
					"pagination_html"	=>	$pagination_html
				)
			);
		}
		else
		{
			dev::$tpl->parse(
				'vps',
				'client_browse_ips_bot',
				array(
					"pagination_html"	=>	$pagination_html
				)
			);
		}
	}

	private static function get_search_columns($sel_column=""){

		$columns = array(
				"ip_addr"	=>	"IP Address"
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
				'vps',
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
				'vps',
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
