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

class vw_ip_pools {

	public static function title($title){

		dev::$tpl->parse(
			'ip_pools',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){

		dev::$tpl->parse(
			'ip_pools',
			'form',
			$fields
		);
	}

	public static function load_browse_js(){

		dev::$js->add_library('apps/ip_pools/js/ip_pools_browse.js');
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

		dev::$tpl->parse(
			'ip_pools',
			'browse_top',
			array(
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_ip_pools::items_per_page($items_col)
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
				
				$tags = array(
					"ip_pool_id"			=>		$item->get_ip_pool_id(),
					"name"					=>		$item->get_name(),
					"first_ip"				=>		$item->get_first_ip(),
					"last_ip"				=>		$item->get_last_ip(),
					"dns"					=>		$item->get_dns(),
					"gateway"				=>		$item->get_gateway(),
					"netmask"				=>		$item->get_netmask(),
					"created"				=>		date('m/d/Y',$item->get_created()),
					"modified"				=>		date('m/d/Y',$item->get_modified()),
					"row"					=>		$row
				);
				
				$tags = array_merge($tags,func_ip_pools::get_assigned($item));

				dev::$tpl->parse(
					'ip_pools',
					'browse_row',
					$tags
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'ip_pools',
				'browse_row_none'
			);
		}

		dev::$tpl->parse(
			'ip_pools',
			'browse_bot',
			array(
				"pagination_html"	=>	$pagination_html
			)
		);
	}

	private static function get_search_columns($sel_column=""){

		$columns = array(
				"ip_pool_id"		=>		"Pool ID",
				"first_ip"			=>		"First IP",
				"last_ip"			=>		"Last IP",
				"dns"				=>		"DNS",
				"gateway"			=>		"Gateway",
				"netmask"			=>		"Netmask"
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
				'ip_pools',
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
				'ip_pools',
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
	
	public static function view_ip_pool($ip_map,$ip_pool,$vps){
	
		dev::$tpl->parse(
			'ip_pools',
			'view_top'
		);
		
		$i = 0;
		foreach($ip_map AS $ip){
		
			if($i % 2 == 0){
				$row = ' odd';
			}
			else
			{
				$row = '';
			}
			
			dev::$tpl->parse(
				'ip_pools',
				'view_row',
				array(
					"row"			=>	$row,
					"ip_addr"		=>	$ip['ip_addr'],
					"ip_pool_id"	=>	$ip_pool->get_ip_pool_id(),
					"name"			=>	$ip_pool->get_name(),
					"vps_id"		=>	$ip['vps_id'],
					"vps_name"		=>	$vps[$ip['vps_id']][0]->get_name()
				)
			);
			
			$i++;
		}
		
		if($i == 0){
			
			dev::$tpl->parse(
				'ip_pools',
				'view_row_none'
			);
		
		}
		
		dev::$tpl->parse(
			'ip_pools',
			'view_bot'
		);
	}

}

?>
