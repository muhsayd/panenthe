<?php
/**
 * Panenthe
 *
 * Very light PHP Framework
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

class dev_pagination {

	static $base_url;
	static $count;
	static $first_page;
	static $last_page;
	static $limit = 10;
	static $next_page;
	static $page;
	static $page_count;
	static $page_html;
	static $page_url;
	static $previous_page;
	static $rows;
	static $row_limit = 10;
	static $row_start;
	static $row_stop;
	static $sql;

	public static function set_page($page){
		self::$page = $page;
	}

	public static function set_base_url($base_url){
		self::$base_url = $base_url;
	}

	public static function set_page_url($page_url){
		self::$page_url = $page_url;
	}

	public static function set_count($count){
		self::$count = $count;
	}

	public static function set_limit($limit){
		self::$limit = $limit;
	}

	public static function set_row_limit($row_limit){
		self::$row_limit = $row_limit;
	}

	public static function get_sql(){
		return self::$sql;
	}

	public static function get_page(){
		return self::$page;
	}

	public static function get_count(){
		return self::$count;
	}

	public static function get_page_html(){
		return self::$page_html;
	}

	public static function generate(){
		self::generate_page_count();
		self::check_page();
		self::generate_sql();
		self::generate_static_links();
		self::generate_range();
		self::generate_pages();
		self::output();
	}

	public static function check_page(){
		if(self::$page > self::$page_count){
			self::$page = self::$page_count;
		}
	}

	public static function generate_sql(){
		if(self::$count > 0){
			self::$sql = 'LIMIT '.((self::$page - 1) * self::$limit).','.self::$limit;
		}
		else
		{
			self::$sql = '';
		}
	}

	public static function generate_page_count(){
		if(self::$limit > 0){
			self::$page_count = ceil(self::$count/self::$limit);
		}
		else
		{
			self::$page_count = 0;
		}
	}

	public static function generate_static_links(){
		$last_url = preg_replace('@{page_no}@si',self::$page_count,self::$page_url);
		$previous_url = preg_replace('@{page_no}@si',(self::$page - 1),self::$page_url);
		$next_url = preg_replace('@{page_no}@si',self::$page_count,self::$page_url);
		$next_url_plus = preg_replace('@{page_no}@si',(self::$page + 1),self::$page_url);


		//First Page
		self::$first_page = '<a href="'.self::$base_url.'" id="pagination_first_page"><<</a>';
		
		//Last Page
		if(self::$page_count > 1){
			self::$last_page = '<a href="'.$last_url.'" id="pagination_last_page">>></a>';
		}
		else
		{
			self::$last_page = '<a href="'.self::$base_url.'" id="pagination_last_page">>></a>';
		}

		//Previous Page
		if(self::$page - 1 > 1){
			self::$previous_page = '<a href="'.$previous_url.'" id="pagination_previous_page"><</a>';
		}
		else
		{
			self::$previous_page = '<a href="'.self::$base_url.'" id="pagination_previous_page"><</a>';
		}

		//Next Page
		if((self::$page + 1) <= self::$page_count && self::$page_count > 1){

			self::$next_page = '<a href="'.$next_url_plus.'" id="pagination_next_page">></a>';

		}
		else
		if(self::$page == self::$page_count){

			self::$next_page = '<a href="'.$next_url.'" id="pagination_next_page">></a>';

		}
		else
		{
			self::$next_page = '<a href="'.self::$base_url.'" id="pagination_next_page">></a>';

		}

	}

	public static function generate_range(){

		self::$row_start = 1;
		self::$row_stop = self::$row_limit;
		$row_dir = ceil(self::$row_limit / 2);

		if(self::$page > $row_dir && self::$page < (self::$page_count - $row_dir)){

			self::$row_start = self::$page - $row_dir;
			self::$row_stop = self::$row_start + self::$row_limit;

		}
		elseif(self::$page > self::$row_limit && self::$page > (self::$page_count - self::$row_limit)){

			self::$row_start = self::$page_count - self::$row_limit;
			self::$row_stop = self::$page_count;

		}
		elseif(self::$page_count <= self::$row_limit){

			self::$row_stop = self::$page_count;

		}

		if(self::$row_stop < 2){
			self::$row_stop = 1;
		}

	}

	public static function generate_pages(){

		self::$rows = '';
		for($i=self::$row_start;$i<=self::$row_stop;$i++){
			$page_url = preg_replace('@{page_no}@si',$i,self::$page_url);
			if($i == self::$page){
				self::$rows .= '<b id="pagination_'.$i.'">'.$i.'</b> ';
			}
			else
			{
				self::$rows .= '<a href="'.$page_url.'" id="pagination_'.$i.'">'.$i.'</a> ';
			}
		}

	}

	public static function output(){
		self::$page_html =
			self::$first_page.' '
			.self::$previous_page.' '
			.self::$rows.' '
			.self::$next_page.' '
			.self::$last_page;
	}

}

?>