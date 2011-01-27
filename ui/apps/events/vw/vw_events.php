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

class vw_events {

	public static function title($title){

		dev::$tpl->parse(
			'events',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){

		dev::$tpl->parse(
			'events',
			'form',
			$fields
		);
	}

	public static function load_browse_js(){

		dev::$js->add_library('apps/events/js/events_browse.js');
	}

	public static function browse($items,$pagination_html,$ack=false){

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

		if($ack){
			$browse_sec = 'browse_ack';
		}
		else
		{
			$browse_sec = 'browse_events';
		}

		dev::$tpl->parse(
			'events',
			'browse_top',
			array(
				"browse_sec"		=>	$browse_sec,
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_events::items_per_page($items_col)
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

				if($ack){
					$row_action_type = 'remove_event';
					$row_action = 'Remove Event';
				}
				else
				{
					$row_action_type = 'acknowledge_event';
					$row_action = 'Acknowledge Event';
				}

				dev::$tpl->parse(
					'events',
					'browse_row',
					array(
							"event_id"			=>		$item->get_event_id(),
							"message"			=>		nl2br($item->get_message()),
							"time"				=>		date('m/d/Y g:i:s a',$item->get_time()),
							"is_acknowledged"	=>		$item->get_is_acknowledged(),
							"created"			=>		$item->get_created(),
							"modified"			=>		$item->get_modified(),
							"row"				=>		$row,
							"row_action"		=>		$row_action,
							"row_action_type"	=>		$row_action_type
					)
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'events',
				'browse_row_none'
			);
		}

		if($ack){
			$browse_action_type = 'browse_action_delete';
			$browse_action = 'Remove Events';
		}
		else
		{
			$browse_action_type = 'browse_action_acknowledge';
			$browse_action = 'Acknowledge Events';
		}

		dev::$tpl->parse(
			'events',
			'browse_bot',
			array(
				"browse_action_type"	=>	$browse_action_type,
				"browse_action"			=>	$browse_action,
				"pagination_html"		=>	$pagination_html
			)
		);
	}

	private static function get_search_columns($sel_column=""){

		$columns = array(
				"event_id"			=>		"Event Id",
				"message"			=>		"Message"
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
				'events',
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
				'events',
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
