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


require_once(main::$root.'/apps/ip_pools/ctl/ctl_browse_ip_pools.php');
require_once(main::$root.'/apps/ip_pools/ctl/ctl_insert_ip_pool.php');
require_once(main::$root.'/apps/ip_pools/ctl/ctl_update_ip_pool.php');
require_once(main::$root.'/apps/ip_pools/ctl/ctl_view_ip_pool.php');
require_once(main::$root.'/apps/ip_pools/ctl/ctl_remove_ip_pool.php');
require_once(main::$root.'/apps/ip_pools/func/func_ip_pools.php');
require_once(main::$root.'/apps/ip_pools/func/verify_ip_pools.php');
require_once(main::$root.'/apps/ip_pools/vw/vw_ip_pools.php');
require_once(main::$root.'/apps/vps/func/func_vps.php');
require_once(main::$root.'/lib/dao/dao_ip_pools.php');
require_once(main::$root.'/lib/dao/dao_servers.php');
require_once(main::$root.'/lib/dao/dao_vps.php');
require_once(main::$root.'/lib/vo/vo_ip_pools.php');
require_once(main::$root.'/lib/vo/vo_servers.php');
require_once(main::$root.'/lib/vo/vo_vps.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');

class idx_ip_pools {

	public function __construct(){
		main::check_permissions('ip_pools');
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
			case 'view_ip_pool':
				new ctl_view_ip_pool();
			break;
			case 'insert_ip_pool':
				new ctl_insert_ip_pool();
			break;
			case 'remove_ip_pool':
				new ctl_remove_ip_pool();
				new ctl_browse_ip_pools();
			break;
			case 'update_ip_pool':
				new ctl_update_ip_pool();
			break;
			case 'browse_ip_pools':
				new ctl_browse_ip_pools();
			break;
			default:
				new ctl_browse_ip_pools();
			break;
		}
	}
}

?>
