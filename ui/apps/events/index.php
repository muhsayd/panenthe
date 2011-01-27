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


require_once(main::$root.'/apps/events/ctl/ctl_browse_events.php');
require_once(main::$root.'/apps/events/ctl/ctl_insert_event.php');
require_once(main::$root.'/apps/events/ctl/ctl_update_event.php');
require_once(main::$root.'/apps/events/ctl/ctl_remove_event.php');
require_once(main::$root.'/apps/events/func/func_events.php');
require_once(main::$root.'/apps/events/func/verify_events.php');
require_once(main::$root.'/apps/events/vw/vw_events.php');
require_once(main::$root.'/lib/dao/dao_events.php');
require_once(main::$root.'/lib/vo/vo_events.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_events {

	public function __construct(){

		main::check_permissions('events');
		$this->page_header();
		$this->action_message();
		$this->handle_sec();
		$this->page_footer();
	}

	private function action_message(){

		main::action_message();
	}

	private function page_header(){

		main::page_header();
	}

	private function page_footer(){

		main::page_footer();
	}

	private function handle_sec(){

		if(isset(dev::$get['sec'])){
			$sec = dev::$get['sec'];
		}
		else
		{
			$sec = '';
		}

		switch($sec){
			case 'insert_event':
				new ctl_insert_event();
			break;
			case 'acknowledge_event':
				func_events::acknowledge_event();
				new ctl_browse_events();
			break;
			case 'remove_event':
				new ctl_remove_event();
				new ctl_browse_events();
			break;
			case 'update_event':
				new ctl_update_event();
			break;
			case 'browse_ack':
				new ctl_browse_events(true);
			break;
			case 'browse_events':
				new ctl_browse_events();
			break;
			default:
				new ctl_browse_events();
			break;
		}
	}
}

?>
