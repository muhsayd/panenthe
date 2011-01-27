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

class func_servers {

	public static function get_kernel_images($name,$value,$server){
	
		$driver = dao_drivers::get_by_driver_id($server->get_driver_id());
		
		if($driver->get_ext_ref() == "ovz"){
			return "Host Kernel";
		}
		
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['server_stats']."
			WHERE server_id = '".$server->get_server_id()."'
			ORDER BY server_stat_id DESC
		");
		
		$rows = array();
		foreach($query->fetchAll() AS $result){
			$rows[$result['name']] = $result['value'];
		}
		
		if(!isset($rows['kernel_images'])){
			//Try to update the server status
			$ops = new server_operations($server);
			$ops->stats_server();
			$ops->execute();
			
			$query = dev::$db->query("
				SELECT * FROM ".main::$cnf['db_tables']['server_stats']."
				WHERE server_id = '".$server->get_server_id()."'
				ORDER BY server_stat_id DESC
			");
		
			$rows = array();
			foreach($query->fetchAll() AS $result){
				$rows[$result['name']] = $result['value'];
			}
			
			if(!isset($rows['kernel_images'])){
				return 'None';
			}
			
		}
		
		$images = explode("\n",$rows['kernel_images']);
		$kernel_images = '';
		foreach($images AS $image){
		
			if(empty($image)){
				continue;
			}
		
			if($image == $value){
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			
			$kernel_images .= dev::$tpl->parse(
				'global',
				'select_row',
				array(
					"selected"	=>	$selected,
					"value"		=>	$image,
					"name"		=>	$image
				),
				true
			);
		}
		
		if(empty($kernel_images)){
			$kernel_images = 'None';
		} else {
			$kernel_images = dev::$tpl->parse(
				'global',
				'select',
				array(
					"name"		=>	$name,
					"options"	=>	$kernel_images
				),
				true
			);
		}
		
		return $kernel_images;
		
	}
		
	public static function get_servers($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_servers::select($where." ORDER BY server_id DESC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array()){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_servers::select($where,$where_values));
	}

	public static function get_server_by_id($server_id){

		return dao_servers::select(
			"WHERE server_id = :server_id",
			array("server_id"=>$server_id)
		);
	}
	
	public static function get_server_ips($server_id){
		
		$ips = array();
		
		$query = dev::$db->query("
			SELECT p.ip_addr AS ip_addr
			FROM ".main::$cnf['db_tables']['vps']." AS m
			LEFT JOIN ".main::$cnf['db_tables']['ip_map']." AS p
			ON p.vps_id = m.vps_id
			WHERE m.server_id = '".$server_id."'
		");
		
		foreach($query->fetchAll() AS $ip){
			$ips[] = $ip['ip_addr'];
		}
		
		foreach($ips AS $key => $ip){
			if(trim($ip) == ''){
				unset($ips[$key]);
			}
		}
		
		return $ips;
		
	}
	
	public static function get_stats($vo){
		
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['server_stats']."
			WHERE server_id = '".$vo->get_server_id()."'
			ORDER BY server_stat_id DESC
		");
		
		$rows = array();
		foreach($query->fetchAll() AS $result){
			$rows[$result['name']] = $result['value'];
		}
		
		$limits = array(
			"memory"	=>	self::hr_size($rows['max_mem']),
			"disk"		=>	self::hr_size($rows['max_disk'])
		);
		
		$stats = array(
			"memory"	=>	self::hr_size($rows['usage_mem'],$limits['memory'][1]),
			"disk"		=>	self::hr_size($rows['usage_disk'],$limits['disk'][1]),
			"load_1"	=>	$rows['load_average_1'],
			"load_5"	=>	$rows['load_average_5'],
			"load_15"	=>	$rows['load_average_15'],
			"uptime"	=>	$rows['uptime'],
			"cpu"		=>	self::cpu_stats($rows['cpuinfo']),
			"vms"		=>	self::get_server_vms($vo)
		);
		
		//Setup Percents
		if($rows['max_mem'] > 0){
			$stats['memory_pct'] = number_format(
				(($rows['usage_mem'] / $rows['max_mem']) * 100),
				0
			);
		}
		else
		{
			$stats['memory_pct'] = 0;
		}
		
		if($rows['max_disk'] > 0){
			$stats['disk_pct'] = number_format(
				(($rows['usage_disk'] / $rows['max_disk']) * 100),
				0
			);
		}
		else
		{
			$stats['disk_pct'] = 0;
		}
		
		//Setup Remaining Percents
		$stats['memory_rep'] = 100 - $stats['memory_pct'];
		$stats['disk_rep'] = 100 - $stats['disk_pct'];
		
		$stats = array_merge($stats,self::allocated_stats($vo));
		
		return array($stats,$limits);
		
	}
	
	public static function cpu_stats($info){
	
		$cpus = array();
		$cpu_chunks = explode("\n\n\n",$info);
		foreach($cpu_chunks AS $cpu_id => $cpu){
			if(trim($cpu) != ""){
				$cpu = trim($cpu);
				$lines = explode("\n\n",$cpu);
				foreach($lines AS $line){
					$nv = explode(':',$line);
					$cpus[$cpu_id][trim($nv[0])] = trim($nv[1]);
				}
			}
		}
		
		return $cpus;
	}
	
	public static function get_server_vms($vo){
	
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['vps']." 
			WHERE server_id = '".$vo->get_server_id()."' 
		");
		
		return $query->rowCount();
		
	}
	
	public static function allocated_stats($vo){
	
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['vps']."
			WHERE server_id = '".$vo->get_server_id()."'
		");
		
		$rows = array(
			"guar_mem"		=>	"",
			"burst_mem"		=>	"",
			"udisk"			=>	"",
			"out_bw"		=>	"",
			"in_bw"			=>	""
		);
		
		$data = array(
			"disk_space"	=>	0,
			"g_mem"			=>	0,
			"b_mem"			=>	0,
			"out_bw"		=>	0,
			"in_bw"			=>	0
		);
		
		foreach($query->fetchAll() AS $result){
			$data['disk_space'] += $result['disk_space'];
			$data['g_mem'] += $result['g_mem'];
			$data['b_mem'] += $result['b_mem'];
			$data['out_bw'] += $result['out_bw'];
			$data['in_bw'] += $result['in_bw'];
		}
		
		$rows['guar_mem'] = self::hr_size($data['g_mem']);
		$rows['burst_mem'] = self::hr_size($data['b_mem']);
		$rows['udisk'] = self::hr_size($data['disk_space']);
		$rows['out_bw'] = self::hr_size($data['out_bw']);
		$rows['in_bw'] = self::hr_size($data['in_bw']);
		
		return $rows;
		
	}
	
	public static function hr_size($size,$override=false){
		
		$den = array(
			"kb"	=>	1,
			"mb"	=>	1024,
			"gb"	=>	1024 * 1024,
			"tb"	=>	1024 * 1024 * 1024
		);
		
		if($override !== false && array_key_exists($override,$den)){
			$hr_size = round($size / $den[$override],2);
			return array($hr_size,$override);
		}
		
		if($size > $den['gb'] * 1000){
			$hr_size = round($size / $den['tb'],2);
			return array($hr_size,'tb');
		}
		else
		if($size > $den['mb'] * 1000){
			$hr_size = round($size / $den['gb'],2);
			return array($hr_size,'gb');
		}
		else
		if($size > $den['kb'] * 1000){
			$hr_size = round($size / $den['mb'],2);
			return array($hr_size,'mb');
		}
		else
		{
			return array(round($size,2),'kb');
		}
	
	}

	public static function get_parent_servers($name,$value){

	    $rows = dao_servers::select('WHERE parent_server_id = 0');
	    $options = '';
		if(count($rows) > 0){
			foreach($rows AS $result){

				if($result->get_server_id() == $value){
					$selected = ' selected="selected"';
				}
				else
				{
					$selected = '';
				}

				$options .= dev::$tpl->parse(
					'global',
					'select_row',
					array(
					"selected"  =>	$selected,
					"value"	    =>	$result->get_server_id(),
					"name"	    =>	$result->get_name()
					),
					true
				);

			}
			
			 $parent_server_id = dev::$tpl->parse(
				'global',
				'select',
				array(
					"name"	=>	$name,
					"options"	=>	$options
				),
				true
			);



		}
		else
		{

			$parent_server_id = dev::$tpl->parse(
				'global',
				'disabled_input',
				array(
					'name'	=>	$name,
					'value'	=>	'0'
				),
				true
			);

		}

		return $parent_server_id;
	   
	}

	public static function save_server(){

		if(isset(dev::$post["server_id"]) && verify_servers::insert() ){
			
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$vo_servers = new vo_servers(dev::$post);
			
			//TODO: Dont know when we will start locking shit.... but until then
			$vo_servers->set_is_locked(0);
			//END
			
			$vo_servers->set_modified(time());
			if(empty(dev::$post["server_id"])){
				
				//Insert Into The Database
				$vo_servers->set_created(time());
				dao_servers::insert($vo_servers->insert_array());
				$vo_servers->set_server_id(dev::$db->lastInsertId());
				
				//Get the Driver
				$serverDriver = new server_operations($vo_servers,true);
				$serverDriver->add_server();
				$serverDriver->execute();
				
				if($serverDriver->isOkay()){
				
					event_api::add_event('Server #'.$vo_servers->get_server_id().' "'.$vo_servers->get_name().'" was added.');
					main::set_action_message("Server has been added!");
					
				}
				else
				{
					
					//Delete from DB
					dao_servers::remove(
						" WHERE server_id = :server_id",
						array("server_id"=>$vo_servers->get_server_id())
					);
					main::set_action_message(
						func_server_home::get_srv_action_message(
							$serverDriver,
							"Server has failed to be added!"
						)
					);
				}
			}
			else
			{
				dao_servers::update(
					$vo_servers->update_array(),
					" WHERE server_id = :v_server_id ",
					array("v_server_id"=>$vo_servers->get_server_id())
				);
				event_api::add_event('Server #'.$vo_servers->get_server_id().' "'.$vo_servers->get_name().'" was updated.');
				main::set_action_message("Server has been saved!");
			}
		}
	}

	public static function remove_server(){

		if(isset(dev::$get["remove_server"])){
			
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			$vo_servers = func_servers::get_server_by_id(dev::$get["remove_server"]);
			$vo_servers = $vo_servers[0];
			//Get the Driver
			$serverDriver = new server_operations($vo_servers,true);
			$serverDriver->remove_server();
			$serverDriver->execute();
			
			//Check for Force Delete
			if(isset(dev::$post['force_delete']) && dev::$post['force_delete'] == 'true'){
				$force_delete = true;
			}
			else
			{
				$force_delete = false;
			}
			
			if($serverDriver->isOkay() || $force_delete){
				event_api::add_event('Server #'.$vo_servers->get_server_id().' "'.$vo_servers->get_name().'" was removed.');
				dao_servers::remove(
					" WHERE server_id = :server_id",
					array("server_id"=>dev::$get["remove_server"])
				);
				dao_vps::remove(
					" WHERE server_id = :server_id",
					array("server_id"=>dev::$get['remove_server'])
				);
				main::set_action_message(
					func_server_home::get_srv_action_message(
						$serverDriver,
						"Server has been removed!"
					)
				);
			}
			else
			{
				main::set_action_message(
					func_server_home::get_srv_action_message(
						$serverDriver,
						"Server has failed to be removed."
					)
				);
			}
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
		
			if(
				main::$cnf['ui_config']['restricted_serverctl'] == "true" &&
				main::$cnf['ui_config']['root_admin_id'] != dev::$tpl->get_constant('cur_admin_id')
			){
			   main::error_page("Only the root admin can manage servers!");
			}
			
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_servers = func_servers::get_server_by_id($delete);
					$vo_servers = $vo_servers[0];
					
					//Get the Driver
					$serverDriver = new server_operations($vo_servers,true);
					$serverDriver->remove_server();
					$serverDriver->execute();
					
					//Check for Force Delete
					if(isset(dev::$post['force_delete']) && dev::$post['force_delete'] == 'true'){
						$force_delete = true;
					}
					else
					{
						$force_delete = false;
					}
			
					if($serverDriver->isOkay() || $force_delete){
						event_api::add_event('Server #'.$vo_servers->get_server_id().' "'.$vo_servers->get_name().'" was removed.');
						dao_servers::remove(
							" WHERE server_id = :server_id",
							array("server_id"=>$delete)
						);
						dao_vps::remove(
							" WHERE server_id = :server_id",
							array("server_id"=>$delete)
						);
					}
					else
					{
						main::set_action_message(
							func_server_home::get_srv_action_message(
								$serverDriver,
								"Server has failed to be removed."
							)
						);
						break;
					}
				}
				main::set_action_message('Servers has been removed!');
			}
		}
	}

}

?>
