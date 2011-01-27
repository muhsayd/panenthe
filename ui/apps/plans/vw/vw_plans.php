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

class vw_plans {

	public static function title($title){

		dev::$tpl->parse(
			'plans',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){

		dev::$tpl->parse(
			'plans',
			'form',
			$fields
		);
	}

	public static function load_browse_js(){

		dev::$js->add_library('apps/plans/js/plans_browse.js');
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
			'plans',
			'browse_top',
			array(
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_plans::items_per_page($items_col)
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

				dev::$tpl->parse(
					'plans',
					'browse_row',
					array(
							"plan_id"				=>		$item->get_plan_id(),
							"name"					=>		$item->get_name(),
							"disk_space"			=>		$item->get_disk_space(),
							"backup_space"			=>		$item->get_backup_space(),
							"swap_space"			=>		$item->get_swap_space(),
							"g_mem"					=>		$item->get_g_mem(),
							"b_mem"					=>		$item->get_b_mem(),
							"cpu_pct"				=>		$item->get_cpu_pct(),
							"cpu_num"				=>		$item->get_cpu_num(),
							"out_bw"				=>		$item->get_out_bw(),
							"in_bw"					=>		$item->get_in_bw(),
							"created"				=>		date('m/d/Y',$item->get_created()),
							"modified"				=>		date('m/d/Y',$item->get_modified()),
						"row"						=>		$row
					)
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'plans',
				'browse_row_none'
			);
		}

		dev::$tpl->parse(
			'plans',
			'browse_bot',
			array(
				"pagination_html"	=>	$pagination_html
			)
		);
	}

	private static function get_search_columns($sel_column=""){

		$columns = array(
				"plan_id"			=>		"Plan Id",
				"name"				=>		"Name",
				"disk_space"			=>		"Disk Space",
				"backup_space"			=>		"Backup Space",
				"swap_space"			=>		"Swap Space",
				"g_mem"			=>		"G Mem",
				"b_mem"			=>		"B Mem",
				"cpu_pct"			=>		"Cpu Pct",
				"cpu_num"			=>		"Cpu Num",
				"out_bw"			=>		"Out Bw",
				"in_bw"			=>		"In Bw"
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
				'plans',
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
				'plans',
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