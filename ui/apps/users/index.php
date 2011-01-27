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


require_once(main::$root.'/apps/users/ctl/ctl_browse_users.php');
require_once(main::$root.'/apps/users/ctl/ctl_insert_user.php');
require_once(main::$root.'/apps/users/ctl/ctl_update_user.php');
require_once(main::$root.'/apps/users/ctl/ctl_remove_user.php');
require_once(main::$root.'/apps/users/func/func_users.php');
require_once(main::$root.'/apps/users/func/verify_users.php');
require_once(main::$root.'/apps/users/vw/vw_users.php');
require_once(dev::$root.'/plugins/dev_pagination.php');
require_once(dev::$root.'/plugins/dev_search.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_users {

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
		
		if(isset(dev::$get['remove_user']) && !empty(dev::$get['remove_user'])){
			new ctl_remove_user();
		}
		
		if(isset(dev::$get['sec'])){
			$sec = dev::$get['sec'];
		}
		else
		{
			$sec = '';
		}
		
		switch($sec){
			
			case 'insert_user':
				new ctl_insert_user();
			break;
			
			case 'update_user':
				new ctl_update_user();
			break;
			
			case 'browse_staff':
				new ctl_browse_users(true);
			break;
			
			case 'browse_staff_online':
				new ctl_browse_users(true,true);
			break;
			
			case 'browse_clients':
				new ctl_browse_users(false);
			break;
			
			case 'browse_clients_online':
				new ctl_browse_users(false,true);
			break;
			
			case 'browse_orphaned_clients':
				new ctl_browse_users(false,false,true);
			break;
			
			default:
				new ctl_browse_users();
			break;
			
		}
	}
}

?>
