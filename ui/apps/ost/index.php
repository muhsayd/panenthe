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

require_once(main::$root.'/apps/vps/func/func_vps.php');
require_once(main::$root.'/apps/ost/ctl/ctl_browse_ost.php');
require_once(main::$root.'/apps/ost/ctl/ctl_insert_ost.php');
require_once(main::$root.'/apps/ost/ctl/ctl_update_ost.php');
require_once(main::$root.'/apps/ost/ctl/ctl_remove_ost.php');
require_once(main::$root.'/apps/ost/func/func_ost.php');
require_once(main::$root.'/apps/ost/func/verify_ost.php');
require_once(main::$root.'/apps/ost/vw/vw_ost.php');
require_once(main::$root.'/lib/dao/dao_ost.php');
require_once(main::$root.'/lib/dao/dao_drivers.php');
require_once(main::$root.'/lib/dao/dao_vps.php');
require_once(main::$root.'/lib/vo/vo_ost.php');
require_once(main::$root.'/lib/vo/vo_drivers.php');
require_once(main::$root.'/lib/vo/vo_vps.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_ost {

	public function __construct(){
		main::check_permissions('ost');
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
			case 'insert_ost':
				new ctl_insert_ost();
			break;
			case 'remove_ost':
				new ctl_remove_ost();
				new ctl_browse_ost();
			break;
			case 'update_ost':
				new ctl_update_ost();
			break;
			case 'browse_ost':
				new ctl_browse_ost();
			break;
			default:
				new ctl_browse_ost();
			break;
		}
	}
}

?>
