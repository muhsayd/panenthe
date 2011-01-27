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

require_once('ctl/ctl_login.php');
require_once('vw/vw_login.php');
require_once('func/func_login.php');
require_once(main::$root.'/lib/dao/dao_users.php');
require_once(main::$root.'/lib/vo/vo_users.php');
require_once(main::$root.'/apps/events/func/event_api.php');

class idx_login{
	
    public function __construct(){
        $this->login();
    }

	private function login(){
		new ctl_login();
	}

}

?>
