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

require_once(main::$root.'/apps/settings/ctl/ctl_update_settings.php');
require_once(main::$root.'/apps/settings/func/func_settings.php');
require_once(main::$root.'/apps/settings/func/verify_settings.php');
require_once(main::$root.'/apps/settings/vw/vw_settings.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_settings {

	public function __construct(){
		main::check_permissions('settings');
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
		
			case 'update_settings':
			default:
				new ctl_update_settings();
			break;
			
		}
	}
}

?>
