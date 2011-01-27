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

class ctl_populate_pages {

	private $item_count = 100;
	private $item_content = array();

	public function __construct(){
		$this->check_count();
		$this->setup_content();
		$this->insert_pages();
	}

	private function check_count(){
		if(isset(dev::$get['item_count']) && !empty(dev::$get['item_count'])){
			$this->item_count = dev::$get['item_count'];
		}
	}

	private function setup_content(){
		$this->item_content = array(
			"page_name"		=>	"Test {current_count}",
			"page_content"	=>	"This is a test of item {current_count}."
		);
	}

	private function insert_pages(){

		for($i=1;$i<=$this->item_count;$i++){
			$item_content = $this->item_content;
			foreach($item_content AS $col => $value){
				$item_content[$col] = preg_replace('@{current_count}@si',$i,$value);
			}

			dev::$post = $item_content;
			func_pages::save_page();

		}

		main::set_action_message('Items have been populated to the pages.');

	}
}

?>
