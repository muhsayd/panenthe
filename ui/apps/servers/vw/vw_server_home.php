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

class vw_server_home {

	public static function title($title){

		dev::$tpl->parse(
			'servers',
			'title',
			array(
				"title"	=>	$title
			)
		);
	}

	public static function load_js(){
		dev::$js->add_library('js/serverAction.js');
	}

	public static function root_password_form($fields){

		dev::$tpl->parse(
			'servers',
			'root_password_form',
			$fields
		);

	}

	public static function hostname_form($fields){

		dev::$tpl->parse(
			'servers',
			'hostname_form',
			$fields
		);

	}

	public static function setup_keys_form($fields){

		dev::$tpl->parse(
			'servers',
			'setup_keys_form',
			$fields
		);

	}

	public static function install_driver_form($fields){

		dev::$tpl->parse(
			'servers',
			'install_driver_form',
			$fields
		);

	}

	public static function remove_driver_form($fields){

		dev::$tpl->parse(
			'servers',
			'remove_driver_form',
			$fields
		);

	}

	public static function ssh_form($fields){

		dev::$tpl->parse(
			'servers',
			'ssh_form',
			$fields
		);

	}

	public static function details_form($fields){

		dev::$tpl->parse(
			'servers',
			'details_form',
			$fields
		);

	}

	public static function cpu_stats($stats){

		$cpu = $stats['cpu'];
		$cpu_stats = '';
		if(is_array($cpu) && count($cpu)>0){
			/*$i = 1;
			foreach($cpu AS $result){
				$result['processor']++;
				if($i>1){
					$result['row'] = 'even';
					$i=0;
				}
				else
				{
					$result['row'] = 'odd';
				}*/
				$tags = $cpu[0];
				$tags['row'] = 'odd';
				$tags['cpu_count'] = count($cpu);
				$cpu_stats .= dev::$tpl->parse(
					'servers',
					'cpu_stat_row',
					$tags,
					true
				);
				/*$i++;
			}*/
		}

		return $cpu_stats;

	}

	public static function server_home($vo_server,$stats,$limits){

		$tags = $vo_server->update_array();

		//Stats Tags
		$tags['stats_uptime'] = $stats['uptime'];
		$tags['stats_vms'] = $stats['vms'];

		//Load
		$tags['stats_load'] = $stats['load_1'].' '.$stats['load_5'].' '.$stats['load_15'];

		//Memory
		$tags['stats_total_mem'] = $limits['memory'][0];
		$tags['stats_usage_mem'] = $stats['memory'][0];
		$tags['stats_mem_den'] = strtoupper($stats['memory'][1]);
		$tags['stats_mem_pct'] = $stats['memory_pct'];
		$tags['stats_mem_rep'] = $stats['memory_rep'];

		//Disk
		$tags['stats_total_disk_space'] = $limits['disk'][0];
		$tags['stats_usage_disk_space'] = $stats['disk'][0];
		$tags['stats_disk_den'] = strtoupper($stats['disk'][1]);
		$tags['stats_disk_pct'] = $stats['disk_pct'];
		$tags['stats_disk_rep'] = $stats['disk_rep'];

		//Allocated
		$tags['stats_guar_mem'] = $stats['guar_mem'][0];
		$tags['stats_guar_mem_den'] = strtoupper($stats['guar_mem'][1]);
		$tags['stats_burst_mem'] = $stats['burst_mem'][0];
		$tags['stats_burst_mem_den'] = strtoupper($stats['burst_mem'][1]);
		$tags['stats_udisk'] = $stats['udisk'][0];
		$tags['stats_udisk_den'] = strtoupper($stats['udisk'][1]);
		$tags['stats_in_bw'] = $stats['in_bw'][0];
		$tags['stats_in_bw_den'] = strtoupper($stats['in_bw'][1]);
		$tags['stats_out_bw'] = $stats['out_bw'][0];
		$tags['stats_out_bw_den'] = strtoupper($stats['out_bw'][1]);

		//CPU
		$tags['cpu'] = self::cpu_stats($stats);

		//Get Change Hostname for Master
		if($vo_server->get_parent_server_id() == 0){
			$tags['change_hostname'] = dev::$tpl->parse(
				'servers',
				'change_hostname_icon',
				$tags,
				true
			);
			$tags['install_driver'] = '';
			$tags['remove_driver'] = '';
			$tags['setup_network'] = '';
			$tags['setup_keys'] = '';
		}
		else
		{
			$tags['change_hostname'] = '';
			$tags['install_driver'] = dev::$tpl->parse(
				'servers',
				'install_driver_icon',
				$tags,
				true
			);
			$tags['remove_driver'] = dev::$tpl->parse(
				'servers',
				'remove_driver_icon',
				$tags,
				true
			);
			$tags['setup_network'] = dev::$tpl->parse(
				'servers',
				'setup_network_icon',
				$tags,
				true
			);
			$tags['setup_keys'] = dev::$tpl->parse(
				'servers',
				'setup_keys_icon',
				$tags,
				true
			);
		}

		//Get Server Operations
		$tags['server_operations'] = dev::$tpl->parse(
			'servers',
			'server_operations',
			$tags,
			true
		);

		dev::$tpl->parse(
			'servers',
			'server_home',
			$tags
		);

	}

}

?>
