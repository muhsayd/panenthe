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

class vo_plans {

	private $plan_id;
	private $name;
	private $disk_space;
	private $backup_space;
	private $swap_space;
	private $g_mem;
	private $b_mem;
	private $cpu_pct;
	private $cpu_num;
	private $out_bw;
	private $in_bw;
	private $created;
	private $modified;

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

	public function set_plan_id($value){
		$this->plan_id = $value;
	}

	public function set_name($value){
		$this->name = $value;
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

	public function set_out_bw($value){
		$this->out_bw = $value;
	}

	public function set_in_bw($value){
		$this->in_bw = $value;
	}

	public function set_created($value){
		$this->created = $value;
	}

	public function set_modified($value){
		$this->modified = $value;
	}

	public function get_plan_id(){
		return $this->plan_id;
	}

	public function get_name(){
		return $this->name;
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

	public function get_out_bw(){
		return $this->out_bw;
	}

	public function get_in_bw(){
		return $this->in_bw;
	}

	public function get_created(){
		return $this->created;
	}

	public function get_modified(){
		return $this->modified;
	}
	
	
	public function api_array(){

		$records['plan_id'] = $this->get_plan_id();
		$records['name'] = $this->get_name();
		$records["disk_space"] = $this->get_disk_space();
		$records["backup_space"] = $this->get_backup_space();
		$records["swap_space"] = $this->get_swap_space();
		$records["g_mem"] = $this->get_g_mem();
		$records["b_mem"] = $this->get_b_mem();
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["out_bw"] = $this->get_out_bw();
		$records["in_bw"] = $this->get_in_bw();

		return $records;

	}
	
	public function update_array(){

		$records['name'] = $this->get_name();
		$records["disk_space"] = $this->get_disk_space();
		$records["backup_space"] = $this->get_backup_space();
		$records["swap_space"] = $this->get_swap_space();
		$records["g_mem"] = $this->get_g_mem();
		$records["b_mem"] = $this->get_b_mem();
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["out_bw"] = $this->get_out_bw();
		$records["in_bw"] = $this->get_in_bw();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

	public function insert_array(){

		$records['name'] = $this->get_name();
		$records["disk_space"] = $this->get_disk_space();
		$records["backup_space"] = $this->get_backup_space();
		$records["swap_space"] = $this->get_swap_space();
		$records["g_mem"] = $this->get_g_mem();
		$records["b_mem"] = $this->get_b_mem();
		$records["cpu_pct"] = $this->get_cpu_pct();
		$records["cpu_num"] = $this->get_cpu_num();
		$records["out_bw"] = $this->get_out_bw();
		$records["in_bw"] = $this->get_in_bw();
		$records["created"] = $this->get_created();
		$records["modified"] = $this->get_modified();

		return $records;

	}

}

?>
