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

require_once(main::$root.'/apps/servers/func/server_operations.php');
require_once(main::$root.'/apps/vps/ctl/ctl_browse_vps.php');
require_once(main::$root.'/apps/vps/ctl/ctl_browse_ips.php');
require_once(main::$root.'/apps/vps/ctl/ctl_browse_au.php');
require_once(main::$root.'/apps/vps/ctl/ctl_browse_status.php');
require_once(main::$root.'/apps/vps/ctl/ctl_insert_vps.php');
require_once(main::$root.'/apps/vps/ctl/ctl_insert_ips.php');
require_once(main::$root.'/apps/vps/ctl/ctl_insert_au.php');
require_once(main::$root.'/apps/vps/ctl/ctl_update_vps.php');
require_once(main::$root.'/apps/vps/ctl/ctl_remove_vps.php');
require_once(main::$root.'/apps/vps/ctl/ctl_vps_home.php');
require_once(main::$root.'/apps/vps/func/func_vps.php');
require_once(main::$root.'/apps/vps/func/func_vps_ip.php');
require_once(main::$root.'/apps/vps/func/func_vps_user.php');
require_once(main::$root.'/apps/vps/func/func_vps_au.php');
require_once(main::$root.'/apps/vps/func/vps_operations.php');
require_once(main::$root.'/apps/vps/func/verify_vps.php');
require_once(main::$root.'/apps/servers/func/func_servers.php');
require_once(main::$root.'/apps/ost/func/func_ost.php');
require_once(main::$root.'/apps/vps/vw/vw_vps.php');
require_once(main::$root.'/apps/vps/vw/vw_ips.php');
require_once(main::$root.'/apps/vps/vw/vw_au.php');
require_once(main::$root.'/lib/dao/dao_vps.php');
require_once(main::$root.'/lib/dao/dao_drivers.php');
require_once(main::$root.'/lib/dao/dao_ost.php');
require_once(main::$root.'/lib/dao/dao_plans.php');
require_once(main::$root.'/lib/dao/dao_servers.php');
require_once(main::$root.'/lib/dao/dao_users.php');
require_once(main::$root.'/lib/dao/dao_ip_pools.php');
require_once(main::$root.'/lib/vo/vo_vps.php');
require_once(main::$root.'/lib/vo/vo_drivers.php');
require_once(main::$root.'/lib/vo/vo_ost.php');
require_once(main::$root.'/lib/vo/vo_plans.php');
require_once(main::$root.'/lib/vo/vo_servers.php');
require_once(main::$root.'/lib/vo/vo_users.php');
require_once(main::$root.'/lib/vo/vo_ip_pools.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/users/func/func_users.php');

class idx_vps {

	public function __construct(){

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
			case 'vps_home':
				dev::$tpl->set_constant("vps_id",dev::$get['vps_id']);
				new ctl_vps_home();
			break;
			case 'insert_vps':
				if(!main::$is_staff){
					main::error_page("You cannot preform this action.");
				}
				new ctl_insert_vps();
			break;
			case 'remove_vps':
				if(!main::$is_staff){
					main::error_page("You cannot preform this action.");
				}
				new ctl_remove_vps();
				new ctl_browse_vps();
			break;
			case 'update_vps':
				new ctl_update_vps();
			break;
			case 'add_ips':
				if(!main::$is_staff){
					main::error_page("You cannot preform this action.");
				}
				dev::$tpl->set_constant("vps_id",dev::$get['vps_id']);
				new ctl_insert_ips();
			break;
			case 'browse_ips':
				dev::$tpl->set_constant("vps_id",dev::$get['vps_id']);
				new ctl_browse_ips();
			break;
			case 'add_au':
				if(!main::$is_staff){
					main::error_page("You cannot preform this action.");
				}
				dev::$tpl->set_constant("vps_id",dev::$get['vps_id']);
				new ctl_insert_au();
			break;
			case 'browse_au':
				if(!main::$is_staff){
					main::error_page("You cannot preform this action.");
				}
				dev::$tpl->set_constant("vps_id",dev::$get['vps_id']);
				new ctl_browse_au();
			break;
			case 'browse_vps':
				new ctl_browse_vps();
			break;
			case 'status_vps':
				new ctl_browse_status();
			break;
			default:
				new ctl_browse_vps();
			break;
		}
	}
}

?>
