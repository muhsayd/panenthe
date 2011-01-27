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

class func_vps_ip {

	public static function get_ips($vo_vps,$where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " AND ".$where;
		}
		return dao_vps::select_ips($vo_vps,$where." ORDER BY vps_id DESC ".$limit,$where_values);
		
	}

	public static function get_main_ip($vo_vps){
	
		$vps_ips = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['ip_map']." 
			WHERE vps_id = '".$vo_vps->get_vps_id()."' 
			ORDER BY ip_id ASC
			LIMIT 1
		");
		
		$rows = $vps_ips->fetch();
		
		return $rows['ip_addr'];
	
	}
	
	public static function get_all_ips($vo_vps){
		
		$vps_ips = dev::$db->query("
			SELECT i.*, p.* FROM ".main::$cnf['db_tables']['ip_map']." AS i
			LEFT JOIN ".main::$cnf['db_tables']['ip_pools']." AS p
			ON p.ip_pool_id = i.ip_pool_id
			WHERE i.vps_id = '".$vo_vps->get_vps_id()."' 
			ORDER BY i.ip_id ASC
		");
		
		$ips = array();
		foreach($vps_ips->fetchAll() AS $result){
			$ips[] = array(
				"ip"		=>	$result['ip_addr'],
				"gateway"	=>	$result['gateway'],
				"netmask"	=>	$result['netmask'],
				"pool_id"	=>	$result['ip_pool_id'],
				"dns"		=>	$result['dns']
			);
		}
		
		//print_r($ips);
		
		return $ips;
		
	}
	
	public static function get_dns($ip){
	
		//Handle Arrays too
		while(is_array($ip)){
			$ip = each($ip);
			$ip = $ip[1];
		}
		
		//Remove last segment of ip
		$ip = explode('.',$ip);
		unset($ip[3]);
		$ip = implode('.',$ip);
		
		//Get DNS
		$rows = dao_ip_pools::select(
			"WHERE first_ip LIKE :v_ip OR last_ip LIKE :v_ip LIMIT 1",
			array(
				"v_ip"	=>	"%".$ip."%"
			)
		);
		
		if(!isset($rows[0])){
			return main::$cnf['ui_config']['default_dns'];
		}
		
		$pool = $rows[0];
		
		return $pool->get_dns();
		
	}

	public static function map_ips($vps_id,$server_id,$ip_amount,$manual_ips,$return_dns=false){

		//Master IP Array
		$ips = array();
		$dns = false;

		$pool_ids = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['ip_pools_map']."
			WHERE server_id = '".$server_id."'
		");
		$pool_rows = $pool_ids->fetchAll();
		
		if(is_array($pool_rows) && count($pool_rows) > 0){
			foreach($pool_rows AS $pool_id){

				//Get Ip Pools
				$pool_rows = dao_ip_pools::select(
					"WHERE ip_pool_id = :v_ip_pool_id ",
					array("v_ip_pool_id"	=>	$pool_id['ip_pool_id'])
				);

				if(isset($pool_rows[0])){
					$result = $pool_rows[0];
				}
				else
				{
					continue;
				}

				$first_ip = explode('.',$result->get_first_ip());
				$last_ip = explode('.',$result->get_last_ip());

				for($i=$first_ip[3];$i<=$last_ip[3];$i++){
				
					$ip_addr = $first_ip[0].'.'.$first_ip[1].'.'.$first_ip[2].'.'.$i;

					$ips[$ip_addr] = array(
						"pool_id"	=>	$result->get_ip_pool_id(),
						"ip"		=>	$ip_addr,
						"dns"		=>	$result->get_dns(),
						"gateway"	=>	$result->get_gateway(),
						"netmask"	=>	$result->get_netmask()
					);

				}

			}

			//Get Taken IPs
			$query = dev::$db->query("SELECT * FROM ".main::$cnf['db_tables']['ip_map']." ");
			$rows = $query->fetchAll();
			$taken_ips = array();
			foreach($rows AS $result){
				$taken_ips[] = $result['ip_addr'];
			}

			//Check if IP is available
			foreach($ips AS $ip_addr => $ip){
			
				if(in_array($ip['ip'],$taken_ips)){
					unset($ips[$ip_addr]);
				}

			}

			//IP Map Array
			$ips_map = array();
			$assigned = 0;

			//Manually Assign
			$manual_ips = explode("\n",$manual_ips);
			foreach($manual_ips AS $ip){
				if(!empty($ip)){
					$ip = trim($ip);
					if(array_key_exists($ip,$ips)){
						if(!$dns){
							$dns = $ips[$ip]['dns'];
						}
						$ips_map[$ip] = $ips[$ip];
						unset($ips[$ip]);
						$assigned++;
					}
				}
			}

			//Auto Assign
			reset($ips);
			for($i=$assigned;$i<$ip_amount;$i++){
				$ip = each($ips);
				if(!isset($ips[$ip['key']]['dns'])){
					continue;
				}
				if(!$dns){
					$dns = $ips[$ip['key']]['dns'];
				}
				$ips_map[$ip['key']] = $ips[$ip['key']];
			}

			if($return_dns){
				return array($ips_map,$dns);
			}
			else
			{
				return $ips_map;
			}
			
		}
		else
		{
			if($return_dns){
				return array(false,false);
			}
			else
			{
				return false;
			}
		}
		
	}
	
	public static function add_post_ips(){
	
		//Get VPS
		$vo_vps = func_vps::get_vps_by_id(dev::$get["vps_id"]);
		$vo_vps = $vo_vps[0];
	
		//Map Ips
		list($ips_map,$dns) = func_vps_ip::map_ips(
			$vo_vps->get_vps_id(),
			$vo_vps->get_server_id(),
			dev::$post['no_ips'],
			dev::$post['manual_ips'],
			true
		);
		
		//Assign IP's to VPS
		$vpsDriver = new vps_operations($vo_vps);
		$vpsDriver->add_ips($ips_map);
		$vpsDriver->set_dns($dns);
		$vpsDriver->execute();
		
		if($vpsDriver->isOkay() && $ips_map){
			self::insert_mapped_ips($ips_map,$vo_vps->get_vps_id());
			event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" '.dev::$post['no_ips'].' IP Address(es) were added.');
			main::set_action_message("IP addresses were added!");
		}
		else
		if(!$ips_map){
			main::set_action_message("IP addresses could not be added! There appears to be no available IP's.");
		}
		else
		{
			main::set_action_message(func_vps::get_vm_action_message($vpsDriver,"IP addresses could not be added!"));
		}
		
	}
	
	public static function insert_mapped_ips($ips_map,$vps_id){
	
		//Assign IP's
		if(is_array($ips_map)){
			foreach($ips_map AS $ip){

				dev::$db->exec("
					INSERT INTO ".main::$cnf['db_tables']['ip_map']."
					(
						ip_addr,
						ip_pool_id,
						vps_id
					)
					VALUES
					(
						'".$ip['ip']."',
						'".$ip['pool_id']."',
						'".$vps_id."'
					)
				");

			}
		}
		
	}
	
	public static function remove_ip(){

		if(isset(dev::$get["remove_ip"])){
			$vo_vps = func_vps::get_vps_by_id(dev::$get["vps_id"]);
			$vo_vps = $vo_vps[0];
			
			$query = dev::$db->query("
				SELECT * FROM ".main::$cnf['db_tables']['ip_map']." 
				WHERE ip_id = '".dev::$s_get['remove_ip']."' 
			");
			
			$row = $query->fetch();
			$ips_map = array();
			$ips_map[] = array('ip'=>$row['ip_addr']);
			$vpsDriver = new vps_operations($vo_vps);
			$vpsDriver->remove_ips($ips_map);
			$vpsDriver->execute();
			
			if($vpsDriver->isOkay()){
				event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" IP '.$row['ip_addr'].' was removed.');
				dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['ip_map']." WHERE ip_id = '".dev::$s_get['remove_ip']."' ");
				main::set_action_message(func_vps::get_vm_action_message($vpsDriver,"VM IP address has been removed!"));
			}
			else
			{
				main::set_action_message(func_vps::get_vm_action_message($vpsDriver,"VM IP address removal has failed!"));
			}
		}
		
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				$output = '';
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_vps = func_vps::get_vps_by_id(dev::$get['vps_id']);
					$vo_vps = $vo_vps[0];
					
					$query = dev::$db->query("
						SELECT * FROM ".main::$cnf['db_tables']['ip_map']." 
						WHERE ip_id = '".$delete."' 
					");
			
					$row = $query->fetch();
					$ips_map = array();
					$ips_map[] = array('ip'=>$row['ip_addr']);
					
					$vpsDriver = new vps_operations($vo_vps);
					$vpsDriver->remove_ips($ips_map);
					$vpsDriver->execute();
					if($vpsDriver->isOkay()){
						event_api::add_event('Virtual Machine #'.$vo_vps->get_vps_id().' "'.$vo_vps->get_hostname().'" IP Address '.$row['ip_addr'].' was removed.');
						$output .= $vpsDriver->getOutput();
						dev::$db->exec("DELETE FROM ".main::$cnf['db_tables']['ip_map']." WHERE ip_id = '".$delete."' ");
					}
					else
					{
						main::set_action_message(func_vps::get_vm_action_message($vpsDriver,"VM IP Address Removal has failed!"));
						return;
					}
				}
				main::set_action_message($output.'VM IP addresses have been removed!');
			}
		}
	}

}

?>
