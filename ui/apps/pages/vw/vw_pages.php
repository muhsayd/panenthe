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

class vw_pages {
	
	public static function title($title){
		dev::$tpl->parse(
			'pages',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){
		dev::$tpl->parse(
			'pages',
			'form',
			$fields
		);
	}

	public static function load_browse_js(){
		dev::$js->add_library('apps/pages/js/pages_browse.js');
	}

	public static function browse($pages,$pagination_html){
	
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
			'pages',
			'browse_top',
			array(
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_pages::items_per_page($items_col)
			)
		);

		$i = 0;
		if(is_array($pages) && count($pages) > 0){
			foreach($pages AS $page){

				if($i % 2 == 0){
					$row = ' odd';
				}
				else
				{
					$row = '';
				}

				dev::$tpl->parse(
					'pages',
					'browse_row',
					array(
						"page_id"		=>	$page->get_page_id(),
						"page_name"		=>	$page->get_page_name(),
						"page_content"	=>	$page->get_page_content(),
						"row"			=>	$row
					)
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'pages',
				'browse_row_none'
			);
		}

		dev::$tpl->parse(
			'pages',
			'browse_bot',
			array(
				"pagination_html"	=>	$pagination_html
			)
		);
		
	}

	public static function get_search_columns($sel_column=''){
		$columns = array(
			"page_id"		=>	"Page ID",
			"page_name"		=>	"Page Name"
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
				'pages',
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

	public static function items_per_page($sel_items=''){
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
				'pages',
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
