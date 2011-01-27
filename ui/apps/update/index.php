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

require_once('vw/vw_update.php');

class idx_update{

	private $action_message = '';

    public function __construct(){
    	main::check_permissions('update');
		$this->page_header();
		$this->action_message();
		$this->post();
		$this->show_page();
		$this->page_footer();
	}

	private function is_process_running($pid){

		exec("ps $pid", $process);
		return(count($process) >= 2);

	}

	private function post(){

		if(isset(dev::$post['action']) && dev::$post['action'] == 'do_update'){
			$latest_version = $this->get_latest_version();
			
			if(!isset(dev::$session['update_pid'])){
				dev::$session['update_pid'] = shell_exec(
					"sudo /opt/panenthe/scripts/bin/liveupdate.sh /opt/panenthe > /dev/null 2> /dev/null & echo $!"
				);
				main::set_action_message("The update is now running in the background. Check here again for completion.");
			}

		}

	}

	private function page_header(){
		main::page_header();
	}

	private function page_footer(){
		main::page_footer();
	}

	private function action_message(){
		main::action_message();
	}

	public static function get_latest_version(){
		$version = file_get_contents("http://www.panenthe.com/release/latest");
		return trim($version);
	}

	private function show_page(){
		
		if(isset(dev::$session['update_pid'])){
			$is_running = $this->is_process_running(dev::$session['update_pid']);
		} else {
			$is_running = false;
		}
		
		if($is_running){
			main::set_action_message("The update is currently running, check back later.");
		} else {
			unset(dev::$session['update_pid']);
		}
		
		$release_news = file_get_contents(
			"http://www.panenthe.com/main/welcome/raw_news/"
		);
		
		//Get Latest Version
		$latest_version = self::get_latest_version();
		$current_version = dev::$tpl->get_constant('script_version');
		vw_update::live_update(
			$current_version,
			$latest_version,
			$is_running,
			$release_news
		);
		
	}

}

?>
