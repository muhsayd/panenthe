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


require_once(main::$root.'/apps/drivers/ctl/ctl_browse_drivers.php');
require_once(main::$root.'/apps/drivers/ctl/ctl_insert_driver.php');
require_once(main::$root.'/apps/drivers/ctl/ctl_update_driver.php');
require_once(main::$root.'/apps/drivers/ctl/ctl_remove_driver.php');
require_once(main::$root.'/apps/drivers/func/func_drivers.php');
require_once(main::$root.'/apps/drivers/func/verify_drivers.php');
require_once(main::$root.'/apps/drivers/vw/vw_drivers.php');
require_once(main::$root.'/lib/dao/dao_drivers.php');
require_once(main::$root.'/lib/vo/vo_drivers.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_drivers {

	public function __construct(){

		main::check_permissions('drivers');
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
			case 'insert_driver':
				new ctl_insert_driver();
			break;
			case 'remove_driver':
				new ctl_remove_driver();
				new ctl_browse_drivers();
			break;
			case 'update_driver':
				new ctl_update_driver();
			break;
			case 'browse_drivers':
				new ctl_browse_drivers();
			break;
			default:
				new ctl_browse_drivers();
			break;
		}
	}
}

?>
