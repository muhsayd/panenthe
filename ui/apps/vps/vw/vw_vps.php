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

class vw_vps {

	public static function title($title){

		dev::$tpl->parse(
			'vps',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function form($fields){

		dev::$js->add_library('apps/vps/js/plan_autofill.js');
		dev::$js->add_library('apps/vps/js/user_assignment_type.js');

		dev::$tpl->parse(
			'vps',
			'form',
			$fields
		);
		
	}
	
	public static function form_step1($fields){
	
		dev::$tpl->parse(
			'vps',
			'form_step1',
			$fields
		);
		
	}

	public static function limits_form($fields){
		
		dev::$js->add_library('apps/vps/js/plan_autofill.js');

		dev::$tpl->parse(
			'vps',
			'limits_form',
			$fields
		);

	}
	
	public static function create_ost_form($fields){

		dev::$tpl->parse(
			'vps',
			'create_ost_form',
			$fields
		);

	}

	public static function rebuild_form($fields){

		dev::$tpl->parse(
			'vps',
			'rebuild_form',
			$fields
		);

	}
	
	public static function name_form($fields){

		dev::$tpl->parse(
			'vps',
			'name_form',
			$fields
		);

	}

	public static function root_password_form($fields){

		dev::$tpl->parse(
			'vps',
			'root_password_form',
			$fields
		);

	}
	
	public static function user_beancounts($vo_vps,$beancount_rows){
	
		dev::$tpl->parse(
			'vps',
			'user_beancounts_top',
			array(
				'hostname'	=>	$vo_vps->get_hostname()
			)
		);
		
		$i = 1;
		foreach($beancount_rows AS $result){
			
			if($i>1){
				$row = 'even';
				$i = 0;
			}
			else
			{
				$row = 'odd';
			}
			
			dev::$tpl->parse(
				'vps',
				'user_beancounts_row',
				array(
					'resource'	=>	$result['resource'],
					'held'		=>	$result['held'],
					'maxheld'	=>	$result['maxheld'],
					'barrier'	=>	$result['barrier'],
					'limit'		=>	$result['limit'],
					'failcnt'	=>	$result['failcnt'],
					'row'		=>	$row
				)
			);
			$i++;
		}
		
		dev::$tpl->parse(
			'vps',
			'user_beancounts_bot'
		);
		
	}
	
	public static function get_stats($stats,$limits){
		$tags = array();
		//Memory
		$tags['g_mem'] 			= $limits['g_mem'][0];
		$tags['memory_usage'] 	= $stats['memory_usage'][0];
		$tags['memory_den'] 	= strtoupper($limits['g_mem'][1]);
		$tags['memory_pct'] 	= $stats['memory_pct'];
		$tags['memory_rep'] 	= $stats['memory_rep'];
		
		//Disk Space
		$tags['disk_space'] 	= $limits['disk_space'][0];
		$tags['disk_usage'] 	= $stats['disk_usage'][0];
		$tags['disk_den'] 		= strtoupper($limits['disk_space'][1]);
		$tags['disk_pct'] 		= $stats['disk_pct'];
		$tags['disk_rep'] 		= $stats['disk_rep'];
		
		//Backup Space
		$tags['backup_space'] 	= $limits['backup_space'][0];
		$tags['backup_usage'] 	= $stats['backup_usage'][0];
		$tags['backup_den'] 	= strtoupper($limits['backup_space'][1]);
		$tags['backup_pct'] 	= $stats['backup_pct'];
		$tags['backup_rep'] 	= $stats['backup_rep'];
		
		//Outgoing Traffic
		$tags['out_bw'] 		= $limits['out_bw'][0];
		$tags['out_bw_usage'] 	= $stats['out_bw_usage'][0];
		$tags['out_bw_den'] 	= strtoupper($limits['out_bw'][1]);
		$tags['out_bw_pct'] 	= $stats['out_bw_pct'];
		$tags['out_bw_rep'] 	= $stats['out_bw_rep'];
		
		//Incoming Traffic
		$tags['in_bw'] 			= $limits['in_bw'][0];
		$tags['in_bw_usage'] 	= $stats['in_bw_usage'][0];
		$tags['in_bw_den'] 		= strtoupper($limits['in_bw'][1]);
		$tags['in_bw_pct'] 		= $stats['in_bw_pct'];
		$tags['in_bw_rep'] 		= $stats['in_bw_rep'];
		
		//Load Average
		$tags['load_average_1']	= $stats['load_average_1'];
		$tags['load_average_5'] = $stats['load_average_5'];
		$tags['load_average_15'] = $stats['load_average_15'];
		$tags['uptime'] = $stats['uptime'];
		
		return $tags;
	}
	
	public static function get_status_history($vo_vps){
		
		$tags = array();
		$status = func_vps::get_status_history($vo_vps);
		$tags['status_code'] = $status;
		
		switch($status){
					
			case '0901':
				$tags['status'] = 'Running';
				$tags['sicon'] = 'icons/16x16/actions/adept_commit.png';
				break;
				
			case '0902':
				$tags['status'] = 'Stopped';
				$tags['sicon'] = 'icons/16x16/actions/messagebox_critical.png';
				break;
				
			case '0903':
				$tags['status'] = 'Deleted';
				$tags['sicon'] = 'icons/16x16/actions/button_cancel.png';
				break;
				
			default: 
				$tags['status'] = 'None';
				$tags['sicon'] = 'icons/16x16/actions/messagebox_info.png';
				break;
		}
		
		return $tags;
	
	}

	public static function vps_home($vo_vps,$status,$stats,$limits,$server,$driver){

		$tags = $vo_vps->home_array();
		$tags['status_code'] = $status['code'];
		$tags['status_severity'] = $status['severity'];
		$tags['status_message'] = $status['message'];
		
		$tags['created'] = date('m/d/Y g:m A',$vo_vps->get_created());
		$tags['modified'] = date('m/d/Y g:m A',$vo_vps->get_modified());
		
		$tags['main_ipaddress'] = func_vps_ip::get_main_ip($vo_vps);
		$tags['vps_id'] = $vo_vps->get_vps_id();
		$tags['real_id'] = $vo_vps->get_real_id();
		
		$tags['server_name'] = $server->get_name();
		
		$tags['driver_ext_ref'] = $driver->get_ext_ref();
		$tags['driver_name'] = $driver->get_name();
		
		$tags = array_merge($tags,self::get_stats($stats,$limits));
		
		switch($status['code']){
					
			case '0901':
				$tags['status'] = 'Running';
				$tags['sicon'] = 'icons/32x32/actions/adept_commit.png';
				break;
				
			case '0902':
				$tags['status'] = 'Stopped';
				$tags['sicon'] = 'icons/32x32/actions/messagebox_critical.png';
				break;
				
			case '0903':
				$tags['status'] = 'Deleted';
				$tags['sicon'] = 'icons/32x32/actions/button_cancel.png';
				break;
				
			default: 
				$tags['status'] = 'None';
				$tags['sicon'] = 'icons/32x32/actions/messagebox_info.png';
				break;
		}
		
		//Suspension Status
		if($vo_vps->get_is_suspended() == '1'){
			$tags['status'] = 'Suspended';
			$tags['sicon'] = 'icons/32x32/apps/important.png';
			$tags['suspend_txt'] = 'Unsuspend';
			$tags['suspend_msg'] = dev::$tpl->parse(
				'vps',
				'suspend_msg',
				array(
					'suspend_msg'	=>	"Notice: The VM is currently suspended."
				),
				true
			);
		}
		else
		{
			$tags['suspend_txt'] = 'Suspend';
			$tags['suspend_msg'] = '';
		}
		
		if(main::$is_staff){
			$tags['vm_operations'] = dev::$tpl->parse(
				'vps',
				'staff_vm_operations',
				$tags,
				true
			);
		}
		else
		{
			$tags['vm_operations'] = dev::$tpl->parse(
				'vps',
				'client_vm_operations',
				$tags,
				true
			);
		}
		
		dev::$tpl->parse(
			'vps',
			'vps_home',
			$tags
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

		dev::$tpl->parse(
			'vps',
			'browse_top',
			array(
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_vps::items_per_page($items_col)
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
				
				//Get Server
				$rows = func_vps::get_server_by_id($item->get_server_id());
				if(isset($rows[0])){
					$server = $rows[0]->get_name();
				}
				else
				{
					$server = 'None';
				}
				
				//Get Driver
				$driver = dao_drivers::get_by_driver_id($item->get_driver_id());
				
				$tags = array(
						"vps_id"			=>		$item->get_vps_id(),
						"hostname"			=>		$item->get_hostname(),
						"server"			=>		$server,
						"name"				=>		$item->get_name(),
						"ip_address"		=>		func_vps_ip::get_main_ip($item),
						"real_id"			=>		$item->get_real_id(),
						"disk_space"		=>		$item->get_disk_space(),
						"backup_space"		=>		$item->get_backup_space(),
						"swap_space"		=>		$item->get_swap_space(),
						"g_mem"				=>		$item->get_g_mem(),
						"b_mem"				=>		$item->get_b_mem(),
						"cpu_pct"			=>		$item->get_cpu_pct(),
						"cpu_num"			=>		$item->get_cpu_num(),
						"in_bw"				=>		$item->get_in_bw(),
						"out_bw"			=>		$item->get_out_bw(),
						"ost"				=>		$item->get_ost(),
						"is_running"		=>		$item->get_is_running(),
						"disk_usage"		=>		"0/".$item->get_disk_space(),
						"memory_usage"		=>		"0/".$item->get_g_mem(),
						"bw_usage"			=>		"0/".$item->get_out_bw(),
						"created"			=>		date('m/d/Y',$item->get_created()),
						"modified"			=>		date('m/d/Y',$item->get_modified()),
						"row"				=>		$row
				);
				
				$tags = array_merge($tags,self::get_status_history($item));
				list($stats,$limits) = func_vps::get_stats($item,$tags['status_code']);
				$tags = array_merge($tags,self::get_stats($stats,$limits));
				
				$tags['driver_ext_ref'] = $driver->get_ext_ref();
				$tags['driver_name'] = $driver->get_name();
				
				//Check Suspension
				if($item->get_is_suspended() == '1'){
					$tags['status'] = 'Suspended';
					$tags['sicon'] = 'icons/22x22/apps/important.png';
				}
				
				
				if(main::$is_staff){
					$tags['row_actions'] = dev::$tpl->parse(
						'vps',
						'staff_row_actions',
						$tags,
						true
					);
				}
				else
				{
					$tags['row_actions'] = dev::$tpl->parse(
						'vps',
						'client_row_actions',
						$tags,
						true
					);
				}

				dev::$tpl->parse(
					'vps',
					'browse_row',
					$tags
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'vps',
				'browse_row_none'
			);
		}

		if(main::$is_staff){
			dev::$tpl->parse(
				'vps',
				'browse_bot',
				array(
					"pagination_html"	=>	$pagination_html
				)
			);
		}
		else
		{
			dev::$tpl->parse(
				'vps',
				'client_browse_bot',
				array(
					"pagination_html"	=>	$pagination_html
				)
			);
		}
		
	}
	
	public static function browse_status($items,$pagination_html){

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
			'vps',
			'browse_top_status',
			array(
				"search_columns"	=>	self::get_search_columns($search_col),
				"search_words"		=>	$search_words,
				"pagination_html"	=>	$pagination_html,
				"items_per_page"	=>	vw_vps::items_per_page($items_col)
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
				
				//Get VPS Status
				$item->extra['ost'] = $item->get_ost();
				$item->set_ost($item->extra['ost_id']);
				$vpsDriver = new vps_operations($item);
				$status = $vpsDriver->status_vps();
				$status_code = $status['code'];
				
				//Get Driver
				$driver = dao_drivers::get_by_driver_id($item->get_driver_id());
				
				if($status['code'] == '0901'){
					//$vpsDriver->stats_vps();
					//$vpsDriver->execute();
				}
				
				//Revert OST
				$item->set_ost($item->extra['ost']);
				
				switch($status['code']){
					
					case '0901':
						$status = 'Running';
						$sicon = 'icons/16x16/actions/adept_commit.png';
						break;
						
					case '0902':
						$status = 'Stopped';
						$sicon = 'icons/16x16/actions/messagebox_critical.png';
						break;
						
					case '0903':
						$status = 'Deleted';
						$sicon = 'icons/16x16/actions/button_cancel.png';
						break;
						
					default: 
						$status = 'None';
						$sicon = 'icons/16x16/actions/messagebox_info.png';
						break;
				}
				
				$tags = array(
						"vps_id"			=>		$item->get_vps_id(),
						"hostname"			=>		$item->get_hostname(),
						"name"				=>		$item->get_name(),
						"ip_address"		=>		func_vps_ip::get_main_ip($item),
						"real_id"			=>		$item->get_real_id(),
						"disk_space"		=>		$item->get_disk_space(),
						"backup_space"		=>		$item->get_backup_space(),
						"swap_space"		=>		$item->get_swap_space(),
						"g_mem"				=>		$item->get_g_mem(),
						"b_mem"				=>		$item->get_b_mem(),
						"cpu_pct"			=>		$item->get_cpu_pct(),
						"cpu_num"			=>		$item->get_cpu_num(),
						"in_bw"				=>		$item->get_in_bw(),
						"out_bw"			=>		$item->get_out_bw(),
						"ost"				=>		$item->get_ost(),
						"is_running"		=>		$item->get_is_running(),
						"disk_usage"		=>		"0/".$item->get_disk_space(),
						"memory_usage"		=>		"0/".$item->get_g_mem(),
						"bw_usage"			=>		"0/".$item->get_out_bw(),
						"created"			=>		date('m/d/Y',$item->get_created()),
						"modified"			=>		date('m/d/Y',$item->get_modified()),
						"status"			=>		$status,
						"sicon"				=>		$sicon,
						"row"				=>		$row
				);
				
				list($stats,$limits) = func_vps::get_stats($item,$status_code);
				$tags = array_merge($tags,self::get_stats($stats,$limits));
				
				$tags['driver_ext_ref'] = $driver->get_ext_ref();
				$tags['driver_name'] = $driver->get_name();
		
				dev::$tpl->parse(
					'vps',
					'browse_row_status',
					$tags
				);

				$i++;

			}
		}
		else
		{
			dev::$tpl->parse(
				'vps',
				'browse_row_none_status'
			);
		}

		dev::$tpl->parse(
			'vps',
			'browse_bot_status',
			array(
				"pagination_html"	=>	$pagination_html
			)
		);
		
	}

	private static function get_search_columns($sel_column=""){

		$columns = array(
				"vps_id"			=>		"VPS ID",
				"hostname"			=>		"Hostname",
				"name"				=>		"Name",
				"real_id"			=>		"Real Id",
				"disk_space"			=>		"Disk Space",
				"backup_space"			=>		"Backup Space",
				"swap_space"			=>		"Swap Space",
				"g_mem"			=>		"G Mem",
				"b_mem"			=>		"B Mem",
				"cpu_pct"			=>		"Cpu Pct",
				"cpu_num"			=>		"Cpu Num",
				"in_bw"			=>		"In Bw",
				"out_bw"			=>		"Out Bw",
				"ost"			=>		"Ost",
				"is_running"			=>		"Is Running",
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
