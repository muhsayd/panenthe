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

class func_ost {

	static $os_types = array(
		"WINDOWS"		=>	"Windows",
		"LINUX"			=>	"Linux",
		"OTHER"			=>	"Other"
	);
	
	public static function get_os_type($os_type){
		
		foreach(self::$os_types AS $type_id => $type){
			if($type_id == $os_type){
				return $type;
			}
		}
		
		return 'None';
		
	}

	public static function get_ost($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_ost::select($where." ORDER BY ost_id DESC ".$limit,$where_values);
	}

	public static function get_ost_with_driver($where="",$where_values=array(),$limit=""){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return dao_ost::select_with_driver($where." ORDER BY o.name ASC ".$limit,$where_values);
	}

	public static function get_count($where="",$where_values=array()){

		if(!empty($where)){
			$where = " WHERE ".$where;
		}
		return count(dao_ost::select($where,$where_values));
	}

	public static function get_ost_by_id($ost_id){

		return dao_ost::select(
			"WHERE ost_id = :ost_id",
			array("ost_id"=>$ost_id)
		);
	}

	public static function get_drivers($name,$value){

	    $rows = dao_drivers::select('');
	    $options = '';
	    foreach($rows AS $result){

		if($result->get_driver_id() == $value){
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
			"value"	    =>	$result->get_driver_id(),
			"name"	    =>	$result->get_name()
		    ),
		    true
		);

	    }

	    $driver_id = dev::$tpl->parse(
		'global',
		'select',
		array(
		    "name"	=>	$name,
		    "options"	=>	$options
		),
		true
	    );

	    return $driver_id;
	}

	public static function get_arch($name,$value){

	    $rows = array(
		"0" => array(
		    "id"    =>	"x86",
		    "name"  =>  "x86"
		),
		"1" =>	array(
		    "id"    =>	"x86_64",
		    "name"  =>  "x86_64"
		)
	    );
	    
	    $options = '';
	    foreach($rows AS $result){

		if($result['id'] == $value){
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
			"value"	    =>	$result['id'],
			"name"	    =>	$result['name']
		    ),
		    true
		);

	    }

	    $arch = dev::$tpl->parse(
		'global',
		'select',
		array(
		    "name"	=>	$name,
		    "options"	=>	$options
		),
		true
	    );

	    return $arch;
	}
	
	public static function get_os_type_drop($name,$value){

		$options = '';
		foreach(self::$os_types AS $type_id => $type){

			if($type_id == $value){
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
				"value"	    =>	$type_id,
				"name"	    =>	$type
				),
				true
			);

		}

		$os_types = dev::$tpl->parse(
			'global',
			'select',
			array(
				"name"		=>	$name,
				"options"	=>	$options
			),
			true
		);

		return $os_types;
		
	}

	public static function save_ost(){

		if(isset(dev::$post["ost_id"]) && verify_ost::insert() ){
			$vo_ost = new vo_ost(dev::$post);
			if(empty(dev::$post["ost_id"])){
				dao_ost::insert($vo_ost->insert_array());
				$vo_ost->set_ost_id(dev::$db->lastInsertId());
				event_api::add_event('OS Template #'.$vo_ost->get_ost_id().' "'.$vo_ost->get_name().'" was added.');
				main::set_action_message("Ost has been added!");
			}
			else
			{
				dao_ost::update(
					$vo_ost->update_array(),
					" WHERE ost_id = :v_ost_id ",
					array("v_ost_id"=>$vo_ost->get_ost_id())
				);
				event_api::add_event('OS Template #'.$vo_ost->get_ost_id().' "'.$vo_ost->get_name().'" was updated.');
				main::set_action_message("Ost has been saved!");
			}
		}
	}

	public static function remove_ost($ost_id=false){

		if(isset(dev::$get["remove_ost"])){
			$ost_id = dev::$get['remove_ost'];
		}
		
		if($ost_id == false){
			main::error_page("OST not found.");
		}
		
		//Make sure OS Template has no VMS
		$vo_vps = func_vps::get_by_ost_id($ost_id);
		if(count($vo_vps) > 0){
			main::set_action_message("OS Template cannot be removed when VMs are using it.");
			return;
		}
		
		$vo_ost = func_ost::get_ost_by_id($ost_id);
		$vo_ost = $vo_ost[0];
		event_api::add_event('OS Template #'.$vo_ost->get_ost_id().' "'.$vo_ost->get_name().'" was removed.');
		dao_ost::remove(
			" WHERE ost_id = :ost_id",
			array("ost_id"=>$ost_id)
		);
		main::set_action_message("OS Template has been removed!");

	}

	public static function browse_action_delete(){
		if(isset(dev::$post['browse_action'])){
			if(is_array(dev::$post['browse_action']) && count(dev::$post['browse_action'])){
				foreach(dev::$post['browse_action'] AS $delete){
					self::remove_ost($delete);
				}
			}
		}
	}

}

?>
