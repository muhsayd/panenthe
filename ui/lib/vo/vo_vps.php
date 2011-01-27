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

class vo_vps {

	private $vps_id;
	private $server_id;
	private $driver_id;
	private $name;
	private $hostname;
	private $real_id;
	private $kernel;
	private $disk_space;
	private $backup_space;
	private $swap_space;
	private $g_mem;
	private $b_mem;
	private $cpu_pct;
	private $cpu_num;
	private $in_bw;
	private $out_bw;
	private $ost;
	private $is_running;
	private $is_suspended;
	private $is_locked;
	private $created;
	private $modified;
	public $extra = array();

	public function __construct($populate_array=array()){
		$this->populate($populate_array);
	}

	public function populate($records=array()){
		foreach($records AS $col => $value){
			$function_name = "set_".$col;
			if(method_exists($this,$function_name)){
				$this->$function_name($value);
			}
		}

	}

	public function set_vps_id($value){
		$this->vps_id = $value;
	}

	public function set_server_id($value){
		$this->server_id = $value;
	}

	public function set_driver_id($value){
		$this->driver_id = $value;
	}
	
	public function set_name($value){
		$this->name = $value;
	}

	public function set_hostname($value){
		$this->hostname = $value;
	}

	public function set_real_id($value){
		$this->real_id = $value;
	}
	
	public function set_kernel($value){
		$this->kernel = $value;
	}

	public function set_disk_space($value){
		$this->disk_space = $value;
	}

	public function set_backup_space($value){
		$this->backup_space = $value;
	}

	public function set_swap_space($value){
		$this->swap_space = $value;
	}

	public function set_g_mem($value){
		$this->g_mem = $value;
	}

	public function set_b_mem($value){
		$this->b_mem = $value;
	}

	public function set_cpu_pct($value){
		$this->cpu_pct = $value;
	}

	public function set_cpu_num($value){
		$this->cpu_num = $value;
	}

	public function set_in_bw($value){
		$this->in_bw = $value;
	}

	public function set_out_bw($value){
		$this->out_bw = $value;
	}

	public function set_ost($value){
		$this->ost = $value;
	}

	public function set_is_running($value){
		$this->is_running = $value;
	}
	
	public function set_is_suspended($value){
		$this->is_suspended = $value;
	}
	
	public function set_is_locked($value){
		$this->is_locked = $value;
	}

	public function set_created($value){
		$this->created = $value;
	}

	public function set_modified($value){
		$this->modified = $value;
	}

	public function get_vps_id(){
		return $this->vps_id;
	}

	public function get_server_id(){
		return $this->server_id;
	}

	public function get_driver_id(){
		return $this->driver_id;
	}
	
	public function get_name(){
		return $this->name;
	}

	public function get_hostname(){
		return $this->hostname;
	}

	public function get_real_id(){
		return $this->real_id;
	}
	
	public function get_kernel(){
		return $this->kernel;
	}

	public function get_disk_space(){
		return $this->disk_space;
	}

	public function get_backup_space(){
		return $this->backup_space;
	}

	public function get_swap_space(){
		return $this->swap_space;
	}

	public function get_g_mem(){
		return $this->g_mem;
	}

	public function get_b_mem(){
		return $this->b_mem;
	}

	public function get_cpu_pct(){
		return $this->cpu_pct;
	}

	public function get_cpu_num(){
		return $this->cpu_num;
	}

	public function get_in_bw(){
		return $this->in_bw;
	}

	public function get_out_bw(){
		return $this->out_bw;
	}

	public function get_ost(){
		return $this->ost;
	}

	public function get_is_running(){
		return $this->is_running;
	}
	
	public function get_is_suspended(){
		return $this->is_suspended;
	}

	public function get_is_locked(){
		return $this->is_locked;
	}

	public function get_created(){
		return $this->created;
	}

	public function get_modified(){
		return $this->modified;
	}
	
	public function api_array(){
		
		$records['vps_id'] = $this->get_vps_id();
		$records['driver_id'] = $this->get_driver_id();
		$records['server_id'] = $this->get_server_id();
		$records['name'] = $this->get_name();
		$records["hostname"] = $this->get_hostname();
		$records["kernel"] = $this->get_kernel();
		$records["real_id"] = $this->get_real_id();
		$records["disk_space"] = $this->get_disk_space();
		$records["backup_space"] = $this->get_backup_space();
		$records["swap_space"] = $this->get_swap_space();
		$records["g_mem"] = $this->get_g_mem();
		$records["b_mem"] = $this->get_b_mem();
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["in_bw"] = $this->get_in_bw();
		$records["out_bw"] = $this->get_out_bw();
		$records["ost"] = $this->get_ost();
		$records["is_running"] = $this->get_is_running();
		$records["is_suspended"] = $this->get_is_suspended();
		$records["is_locked"] = $this->get_is_locked();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;
		
	}

	public function update_array(){

		$records['driver_id'] = $this->get_driver_id();
		$records['server_id'] = $this->get_server_id();
		$records['name'] = $this->get_name();
		$records["hostname"] = $this->get_hostname();
		$records["real_id"] = $this->get_real_id();
		$records["kernel"] = $this->get_kernel();
		$records["disk_space"] = $this->get_disk_space();
		$records["backup_space"] = $this->get_backup_space();
		$records["swap_space"] = $this->get_swap_space();
		$records["g_mem"] = $this->get_g_mem();
		$records["b_mem"] = $this->get_b_mem();
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["in_bw"] = $this->get_in_bw();
		$records["out_bw"] = $this->get_out_bw();
		$records["ost"] = $this->get_ost();
		$records["is_running"] = $this->get_is_running();
		$records["is_locked"] = $this->get_is_locked();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}
	
	public function update_is_locked_array(){

		$records["is_locked"] = $this->get_is_locked();
		return $records;

	}

	public function update_limits_array(){

		$records["disk_space"] = $this->get_disk_space() * 1024;
		$records["backup_space"] = $this->get_backup_space() * 1024;
		$records["swap_space"] = $this->get_swap_space() * 1024;
		$records["g_mem"] = $this->get_g_mem() * 1024;
		$records["b_mem"] = $this->get_b_mem() * 1024;
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["in_bw"] = $this->get_in_bw() * 1024 * 1024;
		$records["out_bw"] = $this->get_out_bw() * 1024 * 1024;
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function update_rebuild_array(){

		$records["hostname"] = $this->get_hostname();
		$records["kernel"] = $this->get_kernel();
		$records["ost"] = $this->get_ost();
		$records["modified"] = $this->get_modified();

		return $records;

	}
	
	public function update_name_array(){
		
		$records['name'] = $this->get_name();
		$records["hostname"] = $this->get_hostname();
		return $records;
			
	}
	
	public function update_suspension_array(){
		
		$records['is_suspended'] = $this->get_is_suspended();
		return $records;
		
	}

	public function insert_array(){

		$records['driver_id'] = $this->get_driver_id();
		$records['server_id'] = $this->get_server_id();
		$records['name'] = $this->get_name();
		$records["hostname"] = $this->get_hostname();
		$records["real_id"] = $this->get_real_id();
		$records["kernel"] = $this->get_kernel();
		$records["disk_space"] = $this->get_disk_space() * 1024;
		$records["backup_space"] = $this->get_backup_space() * 1024;
		$records["swap_space"] = $this->get_swap_space() * 1024;
		$records["g_mem"] = $this->get_g_mem() * 1024;
		$records["b_mem"] = $this->get_b_mem() * 1024;
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["in_bw"] = $this->get_in_bw() * 1024 * 1024;
		$records["out_bw"] = $this->get_out_bw() * 1024 * 1024;
		$records["ost"] = $this->get_ost();
		$records["is_running"] = $this->get_is_running();
		$records["is_locked"] = $this->get_is_locked();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function home_array(){

		$records['vps_id'] = $this->get_vps_id();
		$records['driver_id'] = $this->get_driver_id();
		$records['server_id'] = $this->get_server_id();
		$records['name'] = $this->get_name();
		$records["hostname"] = $this->get_hostname();
		$records["real_id"] = $this->get_real_id();
		$records["kernel"] = $this->get_kernel();
		$records["disk_space"] = $this->get_disk_space();
		$records["backup_space"] = $this->get_backup_space();
		$records["swap_space"] = $this->get_swap_space();
		$records["g_mem"] = $this->get_g_mem();
		$records["b_mem"] = $this->get_b_mem();
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["in_bw"] = $this->get_in_bw();
		$records["out_bw"] = $this->get_out_bw();
		$records["ost"] = $this->get_ost();
		$records["is_running"] = $this->get_is_running();
		$records["is_locked"] = $this->get_is_locked();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

}

?>
