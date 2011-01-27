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

class ctl_update_event {

	private $event;
	private $fields;

	public function __construct(){

		$this->post();
		$this->get_event();
		$this->check_fields();
		$this->show_page();
	}

	private function post(){

		if(isset(dev::$post['event_id'])){
			func_events::save_event();
		}
	}

	private function get_event(){

		if(isset(dev::$get['event_id'])){
			$event = func_events::get_event_by_id(dev::$get['event_id']);
			$this->event = $event[0];
		}
		else
		{
			header("Location: ".main::$url."/index.php?app=events&sec=insert");
		}
	}

	private function check_fields(){

			$this->fields['event_id']			=		$this->event->get_event_id();
			$this->fields['message']			=		$this->event->get_message();
			$this->fields['time']			=		$this->event->get_time();
			$this->fields['is_acknowledged']			=		$this->event->get_is_acknowledged();
			$this->fields['created']			=		$this->event->get_created();
			$this->fields['modified']			=		$this->event->get_modified();
	}

	private function show_page(){

		vw_events::title('Update Event');
		vw_events::form($this->fields);
	}

}

?>