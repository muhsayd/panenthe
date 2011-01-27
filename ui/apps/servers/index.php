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

require_once(main::$root.'/apps/drivers/func/func_drivers.php');
require_once(main::$root.'/apps/servers/ctl/ctl_browse_servers.php');
require_once(main::$root.'/apps/servers/ctl/ctl_insert_server.php');
require_once(main::$root.'/apps/servers/ctl/ctl_update_server.php');
require_once(main::$root.'/apps/servers/ctl/ctl_remove_server.php');
require_once(main::$root.'/apps/servers/ctl/ctl_server_home.php');
require_once(main::$root.'/apps/servers/func/func_servers.php');
require_once(main::$root.'/apps/servers/func/func_server_home.php');
require_once(main::$root.'/apps/servers/func/verify_servers.php');
require_once(main::$root.'/apps/servers/func/server_operations.php');
require_once(main::$root.'/apps/servers/vw/vw_servers.php');
require_once(main::$root.'/apps/servers/vw/vw_server_home.php');
require_once(main::$root.'/lib/dao/dao_drivers.php');
require_once(main::$root.'/lib/dao/dao_ip_pools.php');
require_once(main::$root.'/lib/dao/dao_servers.php');
require_once(main::$root.'/lib/dao/dao_vps.php');
require_once(main::$root.'/lib/vo/vo_drivers.php');
require_once(main::$root.'/lib/vo/vo_servers.php');
require_once(main::$root.'/lib/vo/vo_vps.php');
require_once(main::$root.'/lib/vo/vo_ip_pools.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_servers {

	public function __construct(){
		main::check_permissions('servers');
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
			case 'insert_server':
				new ctl_insert_server();
			break;
			case 'remove_server':
				new ctl_remove_server();
				new ctl_browse_servers();
			break;
			case 'update_server':
				new ctl_update_server();
			break;
			case 'browse_servers':
				new ctl_browse_servers();
			break;
			case 'server_home':
				new ctl_server_home();
			break;
			default:
				new ctl_browse_servers();
			break;
		}
	}
}

?>
