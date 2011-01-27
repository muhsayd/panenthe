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

class ctl_insert_event {

	private $fields;

	public function __construct(){

		$this->post();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['event_id'])){
			func_events::save_event();
		}
	}

	private function check_fields(){

		$this->fields = array(
			'event_id'			=>		"",
			'message'			=>		"",
			'time'			=>		"",
			'is_acknowledged'			=>		"",
			'created'			=>		"",
			'modified'			=>		""
		);

		if(isset(dev::$post['event_id'])){
			$this->fields = dev::$post;
		}
	}

	private function show_page(){

		vw_events::title('Insert Event');
		vw_events::form($this->fields);
	}

}

?>