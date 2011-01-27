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

require_once('vw/vw_home.php');

class idx_home{

    public function __construct(){
    	main::check_permissions('home');
		$this->page_header();
		$this->show_page();
		$this->page_footer();
    }

	private function page_header(){
		main::page_header();
	}

	private function page_footer(){
		if(isset(dev::$get['update_license'])){
			main::set_action_message("License information has been updated.");
		}
		main::page_footer();
	}

	private function show_page(){
		vw_home::home();
	}

}

?>
