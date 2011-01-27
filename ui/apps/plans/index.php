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


require_once(main::$root.'/apps/plans/ctl/ctl_browse_plans.php');
require_once(main::$root.'/apps/plans/ctl/ctl_insert_plan.php');
require_once(main::$root.'/apps/plans/ctl/ctl_update_plan.php');
require_once(main::$root.'/apps/plans/ctl/ctl_remove_plan.php');
require_once(main::$root.'/apps/plans/func/func_plans.php');
require_once(main::$root.'/apps/plans/func/verify_plans.php');
require_once(main::$root.'/apps/plans/vw/vw_plans.php');
require_once(main::$root.'/lib/dao/dao_plans.php');
require_once(main::$root.'/lib/vo/vo_plans.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_plans {

	public function __construct(){
		main::check_permissions('plans');
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
			case 'insert_plan':
				new ctl_insert_plan();
			break;
			case 'remove_plan':
				new ctl_remove_plan();
				new ctl_browse_plans();
			break;
			case 'update_plan':
				new ctl_update_plan();
			break;
			case 'browse_plans':
				new ctl_browse_plans();
			break;
			default:
				new ctl_browse_plans();
			break;
		}
	}
}

?>
