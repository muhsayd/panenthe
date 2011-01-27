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

class func_ip_pools {

	public static function get_ip_pools($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_ip_pools::select($where." ORDER BY ip_pool_id DESC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array()){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_ip_pools::select($where,$where_values));
	}

	public static function get_ip_pool_by_id($ip_pool_id){

		return dao_ip_pools::select(
			"WHERE ip_pool_id = :ip_pool_id",
			array("ip_pool_id"=>$ip_pool_id)
		);
	}
	
	public static function get_assigned($vo_ip_pool){
	
		$assigned = 0;
		$total = 0;
		
		//Explode IP Range
		$first_ip = explode('.',$vo_ip_pool->get_first_ip());
		$last_ip = explode('.',$vo_ip_pool->get_last_ip());
		$ips = array();
		
		for($i=$first_ip[3];$i<=$last_ip[3];$i++){
			
			$ip_addr = $first_ip[0].'.'.$first_ip[1].'.'.$first_ip[2].'.'.$i;

			$ips[$ip_addr] = array(
				"pool_id"	=>	$vo_ip_pool->get_ip_pool_id(),
				"ip"		=>	$ip_addr,
				"dns"		=>	$vo_ip_pool->get_dns(),
				"gateway"	=>	$vo_ip_pool->get_gateway(),
				"netmask"	=>	$vo_ip_pool->get_netmask()
			);

		}
		
		$total = count($ips);
		
		//Check Assigned		
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['ip_map']."
			WHERE ip_pool_id = '".$vo_ip_pool->get_ip_pool_id()."' 
		");
		
		$assigned = $query->rowCount();
	
		return array(
			"assigned"	=>	$assigned,
			"total"		=>	$total
		);
		
	}

	public static function get_servers($name,$value){

	    $rows = dao_servers::select('');
	    $options = '';
	    foreach($rows AS $result){

			if($result->get_parent_server_id() != 0){
				if(is_array($value) && in_array($result->get_server_id(),$value)){
					$selected = ' checked="checked"';
				}
				else
				{
					$selected = '';
				}

				$options .= dev::$tpl->parse(
					'global',
					'checkbox_row',
					array(
						"selected"		=>	$selected,
						"name"			=>	$name,
						"value"			=>	$result->get_server_id(),
						"v_name"	    =>	$result->get_name()
						),
					true
				);
			}

		}

	    return $options;
	}

	public static function map_servers($vo_ip_pools,$servers){

		dev::$db->exec("
			DELETE FROM ".main::$cnf['db_tables']['ip_pools_map']."
			WHERE ip_pool_id = '".$vo_ip_pools->get_ip_pool_id()."'
		");

		if(is_array($servers)){
			foreach($servers AS $server_id){
				dev::$db->exec("
					INSERT INTO ".main::$cnf['db_tables']['ip_pools_map']."
					(
						ip_pool_id,
						server_id,
						created,
						modified
					)
					VALUES
					(
						'".$vo_ip_pools->get_ip_pool_id()."',
						'".$server_id."',
						'".time()."',
						'".time()."'
					)
				");
			}
		}
	}

	public static function get_mapped_servers($vo_ip_pools){

		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['ip_pools_map']."
			WHERE ip_pool_id = '".$vo_ip_pools->get_ip_pool_id()."'
		");

		$results = $query->fetchAll();

		$servers = array();
		foreach($results AS $result){
			$servers[] = $result['server_id'];
		}

		return $servers;
		
	}

	public static function save_ip_pool(){

		if(isset(dev::$post["ip_pool_id"]) && verify_ip_pools::insert() ){
			$vo_ip_pools = new vo_ip_pools(dev::$post);
			$vo_ip_pools->set_modified(time());
			if(empty(dev::$post["ip_pool_id"])){
				$vo_ip_pools->set_created(time());
				dao_ip_pools::insert($vo_ip_pools->insert_array());
				$vo_ip_pools->set_ip_pool_id(dev::$db->lastInsertId());
				event_api::add_event('IP Pool #'.$vo_ip_pools->get_ip_pool_id().' "'.$vo_ip_pools->get_name().'" was added.');
				func_ip_pools::map_servers($vo_ip_pools,dev::$post['servers']);
				main::set_action_message("Ip Pool has been added!");
			}
			else
			{
				dao_ip_pools::update(
					$vo_ip_pools->update_array(),
					" WHERE ip_pool_id = :v_ip_pool_id ",
					array("v_ip_pool_id"=>$vo_ip_pools->get_ip_pool_id())
				);
				event_api::add_event('IP Pool #'.$vo_ip_pools->get_ip_pool_id().' "'.$vo_ip_pools->get_name().'" was updated.');
				func_ip_pools::map_servers($vo_ip_pools,dev::$post['servers']);
				main::set_action_message("Ip Pool has been saved!");
			}
		}
	}

	public static function remove_ip_pool(){

		if(isset(dev::$get["remove_ip_pool"])){
			$vo_ip_pools = func_ip_pools::get_ip_pool_by_id(dev::$get['remove_ip_pool']);
			$vo_ip_pools = $vo_ip_pools[0];
			event_api::add_event('IP Pool #'.$vo_ip_pools->get_ip_pool_id().' "'.$vo_ip_pools->get_name().'" was removed.');
			dao_ip_pools::remove(
				" WHERE ip_pool_id = :ip_pool_id",
				array("ip_pool_id"=>dev::$get["remove_ip_pool"])
			);
			main::set_action_message("Ip Pool has been removed!");
		}
	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					$vo_ip_pools = func_ip_pools::get_ip_pool_by_id($delete);
					$vo_ip_pools = $vo_ip_pools[0];
					event_api::add_event('IP Pool #'.$vo_ip_pools->get_ip_pool_id().' "'.$vo_ip_pools->get_name().'" was removed.');
					dao_ip_pools::remove(
						" WHERE ip_pool_id = :ip_pool_id",
						array("ip_pool_id"=>$delete)
					);
				}
				main::set_action_message('Ip Pools has been removed!');
			}
		}
	}

}

?>
