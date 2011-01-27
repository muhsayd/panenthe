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

ob_start();
session_start();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

define("IS_INCLUDED",true);
define("SCRIPT_ROOT",dirname(__file__));

require('base.php');

//Disable Notices in Production
if(
	$config['ui_config']['vps_debug'] == 'false' && 
	$config['ui_config']['server_debug'] == 'false'
){
	error_reporting(0);
}

final class main {

	static $root;
	static $main_root;
	static $url;
	static $uri;
	static $cnf;
	static $err;
	static $nav;
	static $is_staff;
	static $logged_in;

	private $chunk_chars = array(0, 4, 10, 17);

	public function __construct($config,$errors){

		self::$is_staff = false;
		self::$cnf = $config;
		self::$err = $errors;
		$this->paths();
		$this->init();
		$this->handle_login();
		$this->handle_type();

	}
	
	public static function get_err_code($constant){
		foreach(main::$err AS $error){
			if($error['constant'] == $constant){
				return $error;
			}
		}
		return false;
	}

	private function paths(){

		self::$root = SCRIPT_ROOT;
		self::$main_root = SCRIPT_ROOT;
		self::$url = self::$cnf['url'];
		self::$uri = self::$cnf['uri'];

	}

	private function init(){

		require(self::$main_root.'/init/init.php');

		//Append Config
		self::$cnf['dev']['tpl']['tpl_path'] = 'tpl/default';
		self::$cnf['dev']['tpl']['tpl_theme'] = 'tpl/theme';
		$init = new init(self::$cnf['dev']);

	}

	private static function populate_nav(){

		if(self::$is_staff){

			self::$nav[0]['name'] = 'Dashboard';
			self::$nav[0]['app_name'] = false;

			self::$nav[1]['name'] = 'Virtual Machines';
			self::$nav[1]['app_name'] = 'vps';
			self::$nav[1]['sub'][0]['name'] = 'Browse VMs';
			self::$nav[1]['sub'][0]['sec_name'] = 'browse_vps';
			self::$nav[1]['sub'][0]['icon'] = 'icons/22x22/devices/blockdevice.png';
			self::$nav[1]['sub'][1]['name'] = 'Create VM';
			self::$nav[1]['sub'][1]['sec_name'] = 'insert_vps';
			self::$nav[1]['sub'][1]['icon'] = 'icons/22x22/apps/display.png';
			self::$nav[1]['sub'][2]['name'] = 'Live VM Status';
			self::$nav[1]['sub'][2]['sec_name'] = 'status_vps';
			self::$nav[1]['sub'][2]['icon'] = 'icons/22x22/apps/ksysguard.png';

			self::$nav[2]['name'] = 'User Manager';
			self::$nav[2]['app_name'] = 'users';
			self::$nav[2]['sub'][0]['name'] = 'Browse Clients';
			self::$nav[2]['sub'][0]['sec_name'] = 'browse_clients';
			self::$nav[2]['sub'][0]['icon'] = 'icons/22x22/filesystems/folder_open.png';
			self::$nav[2]['sub'][1]['name'] = 'Browse Staff';
			self::$nav[2]['sub'][1]['sec_name'] = 'browse_staff';
			self::$nav[2]['sub'][1]['icon'] = 'icons/22x22/filesystems/folder_home.png';
			self::$nav[2]['sub'][2]['name'] = 'Insert User';
			self::$nav[2]['sub'][2]['sec_name'] = 'insert_user';
			self::$nav[2]['sub'][2]['icon'] = 'icons/22x22/apps/kuser.png';
			self::$nav[2]['sub'][3]['name'] = 'Clients Online';
			self::$nav[2]['sub'][3]['sec_name'] = 'browse_clients_online';
			self::$nav[2]['sub'][3]['icon'] = 'icons/22x22/apps/web.png';
			self::$nav[2]['sub'][4]['name'] = 'Staff Online';
			self::$nav[2]['sub'][4]['sec_name'] = 'browse_staff_online';
			self::$nav[2]['sub'][4]['icon'] = 'icons/22x22/apps/web.png';
			self::$nav[2]['sub'][5]['name'] = 'Orphaned Clients';
			self::$nav[2]['sub'][5]['sec_name'] = 'browse_orphaned_clients';
			self::$nav[2]['sub'][5]['icon'] = 'icons/22x22/apps/access.png';

			self::$nav[3]['name'] = 'IP Pools';
			self::$nav[3]['app_name'] = 'ip_pools';
			self::$nav[3]['sub'][0]['name'] = 'Browse IP Pools';
			self::$nav[3]['sub'][0]['sec_name'] = 'browse_ip_pools';
			self::$nav[3]['sub'][0]['icon'] = 'icons/22x22/filesystems/Globe2.png';
			self::$nav[3]['sub'][1]['name'] = 'Insert IP Pool';
			self::$nav[3]['sub'][1]['sec_name'] = 'insert_ip_pool';
			self::$nav[3]['sub'][1]['icon'] = 'icons/22x22/filesystems/network.png';

			self::$nav[4]['name'] = 'Resource Plans';
			self::$nav[4]['app_name'] = 'plans';
			self::$nav[4]['sub'][0]['name'] = 'Browse Plans';
			self::$nav[4]['sub'][0]['sec_name'] = 'browse_plans';
			self::$nav[4]['sub'][0]['icon'] = 'icons/22x22/apps/background.png';
			self::$nav[4]['sub'][1]['name'] = 'Insert Plan';
			self::$nav[4]['sub'][1]['sec_name'] = 'insert_plan';
			self::$nav[4]['sub'][1]['icon'] = 'icons/22x22/apps/desktopshare.png';


			self::$nav[5]['name'] = 'Server Manager';
			self::$nav[5]['app_name'] = 'servers';
			self::$nav[5]['sub'][0]['name'] = 'Browse Servers';
			self::$nav[5]['sub'][0]['sec_name'] = 'browse_servers';
			self::$nav[5]['sub'][0]['icon'] = 'icons/22x22/devices/hdd_unmount.png';
			self::$nav[5]['sub'][1]['name'] = 'Add Server';
			self::$nav[5]['sub'][1]['sec_name'] = 'insert_server';
			self::$nav[5]['sub'][1]['icon'] = 'icons/22x22/apps/terminal.png';

			self::$nav[6]['name'] = 'Event Manager';
			self::$nav[6]['app_name'] = 'events';
			self::$nav[6]['sub'][0]['name'] = 'Browse Events';
			self::$nav[6]['sub'][0]['sec_name'] = 'browse_events';
			self::$nav[6]['sub'][0]['icon'] = 'icons/22x22/apps/kate.png';
			self::$nav[6]['sub'][1]['name'] = 'Acknowledged';
			self::$nav[6]['sub'][1]['sec_name'] = 'browse_ack';
			self::$nav[6]['sub'][1]['icon'] = 'icons/22x22/apps/hwinfo.png';
			//self::$nav[6]['sub'][2]['name'] = 'Insert Event';
			//self::$nav[6]['sub'][2]['sec_name'] = 'insert_event';
			//self::$nav[6]['sub'][2]['icon'] = 'icons/22x22/apps/important.png';

			self::$nav[7]['name'] = 'Settings';
			self::$nav[7]['app_name'] = 'settings';
			self::$nav[7]['invisible_nav'] = true;
			self::$nav[7]['sub'][0]['name'] = 'Change Settings';
			self::$nav[7]['sub'][0]['sec_name'] = 'update_settings';
			self::$nav[7]['sub'][0]['icon'] = 'icons/22x22/apps/autostart.png';
			
			self::$nav[8]['name'] = 'Drivers';
			self::$nav[8]['app_name'] = 'drivers';
			self::$nav[8]['invisible_nav'] = true;
			self::$nav[8]['sub'][0]['name'] = 'Browse Drivers';
			self::$nav[8]['sub'][0]['sec_name'] = 'browse_drivers';
			self::$nav[8]['sub'][0]['icon'] = 'icons/22x22/apps/kfloppy.png';
			//self::$nav[8]['sub'][1]['name'] = 'Add Driver';
			//self::$nav[8]['sub'][1]['sec_name'] = 'insert_driver';
			//self::$nav[8]['sub'][1]['icon'] = 'icons/22x22/filesystems/file.png';

			self::$nav[9]['name'] = 'OS Templates';
			self::$nav[9]['app_name'] = 'ost';
			self::$nav[9]['invisible_nav'] = true;
			self::$nav[9]['sub'][0]['name'] = 'Browse OST';
			self::$nav[9]['sub'][0]['sec_name'] = 'browse_ost';
			self::$nav[9]['sub'][0]['icon'] = 'icons/22x22/filesystems/folder_documents.png';
			self::$nav[9]['sub'][1]['name'] = 'Add OST';
			self::$nav[9]['sub'][1]['sec_name'] = 'insert_ost';
			self::$nav[9]['sub'][1]['icon'] = 'icons/22x22/mimetypes/document.png';
		}
		else
		{

			self::$nav[1]['name'] = 'Virtual Machines';
			self::$nav[1]['app_name'] = 'vps';
			self::$nav[1]['sub'][0]['name'] = 'Browse VMs';
			self::$nav[1]['sub'][0]['sec_name'] = 'browse_vps';
			self::$nav[1]['sub'][0]['icon'] = 'icons/22x22/devices/blockdevice.png';

		}

	}

	private function handle_login(){
		require(self::$root.'/apps/login/index.php');
		if(isset(dev::$get['logout']) && dev::$get['logout'] == 'true'){
			func_login::do_logout();
			header("Location: ".main::$url.'/index.php');
		}
		else
		{
			if(!func_login::validate_login()){
			   new idx_login();
			   self::$logged_in = false;
			}
			else
			{
				$this->reg_current_admin();
				self::$logged_in = true;
			}
		}

	}

	private function reg_current_admin(){
		$cur_admin = func_login::$current_admin;

		//Ping Last Refresh
		dev::$db->exec("
			UPDATE ".self::$cnf['db_tables']['users']."
			SET last_refresh = '".time()."'
			WHERE user_id = '".$cur_admin->get_user_id()."' LIMIT 1
		");

		$constant_array = array(
			"cur_admin_id"			=>	$cur_admin->get_user_id(),
			"cur_admin_login"		=>	$cur_admin->get_username(),
			"cur_admin_password"		=>	$cur_admin->get_password(),
			"cur_admin_first_name"		=>	$cur_admin->get_first_name(),
			"cur_admin_last_name"		=>	$cur_admin->get_last_name(),
			"cur_admin_email"		=>	$cur_admin->get_email()
		);
		dev::$tpl->set_constants($constant_array);

		//Setup Client Panel
		if($cur_admin->get_is_staff() == 0){
			self::$is_staff = false;
		}
		else
		{
			self::$is_staff = true;
		}

	}

	public static function check_permissions($app){

		$client_apps = array(
			"users",
			"home",
			"login",
			"vps"
		);

		if(self::$is_staff){
			return true;
		}
		else
		{
			//Check If Allowed
			if(in_array($app,$client_apps)){
				return true;
			}
			else
			{
				//Show No Permissions
				self::page_header();

				dev::$tpl->parse(
					'global',
					'no_access'
				);

				self::page_footer();
				exit;
			}
		}

	}

	public static function error_page($message){

		//Show Error
		dev::$tpl->reset_body();

		self::page_header();

		dev::$tpl->set_constant("title_tags","System Error");
		dev::$tpl->parse(
			'global',
			'error_page',
			array(
				"message"	=>	$message
			)
		);

		self::page_footer();
		exit;
	}
	
	public static function notice_page($message){

		//Show Error
		dev::$tpl->reset_body();

		self::page_header();

		dev::$tpl->set_constant("title_tags","System Notice");
		dev::$tpl->parse(
			'global',
			'notice_page',
			array(
				"message"	=>	$message
			)
		);

		self::page_footer();
		exit;
	}

	private function handle_type(){

		if(self::$logged_in === true){

			if(isset(dev::$get['app'])){
				$type = dev::$get['app'];
			}
			else
			{
				$type = '';
				if(!self::$is_staff){
					header("Location: index.php?app=vps");
					exit;
				}
			}
			
			$this->validate_license();
			if(isset(dev::$get['update_license'])){
				$this->update_license();
			}
			
			$query = dev::$db->query("
				SELECT * FROM ".main::$cnf['db_tables']['vps']."
			");

			$total_vms = $query->rowCount();

			//Check Limits
			if($total_vms > main::$cnf['vm_limit']){
				main::error_page(
					'You have exceeded your VM license allowance, please visit '.
					'the <a href="https://clients.panenthe.com">client area</a> '.
					'to purchase more licenses.'
				);
			}

			switch($type){
				case 'events':
					dev::$tpl->set_constant('title_tags','Event Manager');
					require(self::$root.'/apps/events/index.php');
					new idx_events();
				break;
				case 'update':
					dev::$tpl->set_constant('title_tags','Live Update');
					require(self::$root.'/apps/update/index.php');
					new idx_update();
				break;
				case 'welcomeemail':
					dev::$tpl->set_constant('title_tags','Change Welcome Email');
					require(self::$root.'/apps/welcomeemail/index.php');
					new idx_welcomeemail();
				break;
				case 'logviewer':
					dev::$tpl->set_constant('title_tags','View Logs');
					require(self::$root.'/apps/logviewer/index.php');
					new idx_logviewer();
				break;
				case 'vps':
					dev::$tpl->set_constant('title_tags','Virtual Machines');
					require(self::$root.'/apps/vps/index.php');
					new idx_vps();
				break;
				case 'users':
					dev::$tpl->set_constant('title_tags','User Manager');
					require(self::$root.'/apps/users/index.php');
					new idx_users();
				break;
				case 'ip_pools':
					dev::$tpl->set_constant('title_tags','IP Pool Manager');
					require(self::$root.'/apps/ip_pools/index.php');
					new idx_ip_pools();
				break;
				case 'plans':
					dev::$tpl->set_constant('title_tags','Resource Plan Manager');
					require(self::$root.'/apps/plans/index.php');
					new idx_plans();
				break;
				case 'servers':
					dev::$tpl->set_constant('title_tags','Server Manager');
					require(self::$root.'/apps/servers/index.php');
					new idx_servers();
				break;
				case 'settings':
					dev::$tpl->set_constant('title_tags','Settings Manager');
					require(self::$root.'/apps/settings/index.php');
					new idx_settings();
				break;
				case 'drivers':
					dev::$tpl->set_constant('title_tags','Driver Manager');
					require(self::$root.'/apps/drivers/index.php');
					new idx_drivers();
				break;
				case 'ost':
					dev::$tpl->set_constant('title_tags','OS Template manager');
					require(self::$root.'/apps/ost/index.php');
					new idx_ost();
				break;
				default:
					dev::$tpl->set_constant('title_tags','Welcome');
					require(self::$root.'/apps/home/index.php');
					new idx_home();
				break;
				
			}
					
		}

	}

	public static function page_header(){

		list($navbar, $subbar) = self::admin_navbar();

		if(self::$is_staff){
			$topbar_tpl = 'staff_topbar';
		}
		else
		{
			$topbar_tpl = 'client_topbar';
		}

		$topbar = dev::$tpl->parse(
			'header',
			$topbar_tpl,
			array(),
			true
		);
		
		dev::$tpl->set_constant("admin_subbar",$subbar);

		dev::$tpl->parse(
			'header',
			'header',
			array(
				"admin_topbar"		=>	$topbar,
				"admin_navbar"		=>	$navbar
			)
		);

		dev::$css->add_file('main.css');
		dev::$js->add_library('js/mootools-1.2.1-core.js');
		dev::$js->add_library('js/mootools-1.2-more.js');
		dev::$js->add_library('js/loading_link.js');
		dev::$js->add_library('js/header_time.js');
		dev::$js->add_library('js/confirmDelete.js');
		dev::$js->add_library('js/buttonStyle.js');
		dev::$js->add_library('js/checkboxStyle.js');

		dev::$tpl->set_constant('action_message','');

	}

	public static function admin_navbar(){

		self::populate_nav();

		$navbar = '';
		$subbar = '';

		foreach(self::$nav AS $app){
			if(
				(
					isset(dev::$get['app']) && 
					dev::$get['app'] == $app['app_name']
				) 
				||
				(
					!isset(dev::$get['app']) && 
					$app['app_name'] === false
				)
			){
				$selected = true;
				$nav_selected = ' class="disableLoadingLink admin_navbar_selected"';
			}
			else
			{
				$selected = false;
				$nav_selected = ' class="disableLoadingLink"';
			}
			
			if($app['app_name'] !== false){
				$url = main::$url.'/index.php?app='.$app['app_name'];
			}
			else
			{
				$url = main::$url.'/index.php';
			}

			if(!isset($app['invisible_nav']))
				$navbar .= dev::$tpl->parse(
					'global',
					'admin_navbar',
					array(
						"url"		=>	$url,
						"selected"	=>	$nav_selected,
						"name"		=>	$app['name']
					),
					true
				);

			if($selected && $app['app_name'] !== false){
				$sub_selected = false;
				foreach($app['sub'] AS $sub){
					if(
						(isset(dev::$get['sec']) && dev::$get['sec'] == $sub['sec_name']) ||
						(!isset(dev::$get['sec']) && !$sub_selected)
					){
						$sub_selected = true;
						$nav_selected = ' class="disableLoadingLink admin_subbar_selected"';
					}
					else
					{
						$nav_selected = ' class="disableLoadingLink"';
					}

					$subbar .= dev::$tpl->parse(
						'global',
						'admin_subbar',
						array(
							"url"		=>	main::$url.'/index.php?app='.$app['app_name'].'&sec='.$sub['sec_name'],
							"selected"	=>	$nav_selected,
							"icon"		=>	$sub['icon'],
							"name"		=>	$sub['name']
						),
						true
					);
				}
			}
		}

		$navbar = dev::$tpl->parse(
			'header',
			'admin_navbar',
			array(
				'admin_navbar'	=>	$navbar
			),
			true
		);

		if($subbar != ''){
			$subbar = dev::$tpl->parse(
				'header',
				'admin_subbar',
				array(
					'admin_subbar'	=>	$subbar
				),
				true
			);
		}

		return array($navbar, $subbar);
	}

	public static function page_footer(){
		
		dev::$tpl->parse(
			'footer',
			'footer',
			array(
				"performance"	=>	dev::get_performance()
			)
		);
		
		dev::$tpl->set_constant("dbg_output",dev::$dbg_data);
		dev::$js->output();
		dev::$css->output();
		echo dev::$tpl->output();

	}

	public static function system_stats(){

		if(self::$is_staff){

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['events']."
				WHERE is_acknowledged = 0
			");

			$unacknowledged_events = $query->rowCount();

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['users']."
				WHERE is_staff = 0
			");

			$total_clients = $query->rowCount();

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['users']."
				WHERE is_staff = 1
			");

			$total_staff = $query->rowCount();

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['users']."
				WHERE is_staff = 0 AND last_refresh > ".(time() - 900)."
			");

			$clients_online = $query->rowCount();

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['users']."
				WHERE is_staff = 1 AND last_refresh > ".(time() - 900)."
			");

			$staff_online = $query->rowCount();

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['servers']."
			");

			$total_servers = $query->rowCount();

			$query = dev::$db->query("
				SELECT * FROM ".self::$cnf['db_tables']['vps']."
			");

			$total_vms = $query->rowCount();

			$tags = array(
				"licensed_vms"		=>	main::$cnf['vm_limit'],
				"unacknowledged_events"	=>	$unacknowledged_events,
				"total_clients"		=>	$total_clients,
				"total_staff"		=>	$total_staff,
				"clients_online"	=>	$clients_online,
				"staff_online"		=>	$staff_online,
				"total_servers"		=>	$total_servers,
				"servers_online"	=>	$total_servers,
				"total_vms"			=>	$total_vms
			);
			
			$tags['site_url'] = dev::$tpl->get_constant('site_url');
			$tags['script_version'] = dev::$tpl->get_constant('script_version');
			
			//Get Latest Version
			include(main::$root.'/apps/update/index.php');
			$tags['latest_version'] = idx_update::get_latest_version();

			return dev::$tpl->parse(
				'system_stats',
				'system_stats',
				$tags,
				true
			);

		}
		else
		{
			return '';
		}

	}

	public static function action_message(){
		/*dev::$tpl->parse(
			'global',
			'message'
		);*/
	}

	public static function set_action_message($message){
		dev::$tpl->set_constant('action_message', dev::$tpl->parse(
			'global',
			'action_message',
			array(
				"action_message"	=>	$message
			),
			true
		));
	}

	public static function output(){
		dev::$js->output();
		dev::$css->output();
		echo dev::$tpl->output();
	}
	
	private function validate_license($after_update=false){
		
		//Setup License Holder
		main::$cnf['licenses'] = array();
		$license_key_src = @file_get_contents(main::$cnf['main']['root_dir'].'/shared/etc/license');
		
		//Break Apart By Line
		$license_keys = explode("\n",$license_key_src);
		$used_keys = array();
		$used_types = array();
		if(is_array($license_keys) && count($license_keys) > 0){
			foreach($license_keys AS $key){
				if(trim($key) != ''){
					$license = $this->keydec($key);
					//dev::output_r($license,date('m/d/y',$license['date']));
					if(
						$license['date'] >= time() && 
						//!in_array($license['type'],$used_types) &&
						!in_array($key,$used_keys)
					){
						$used_keys[] = $key;
						$used_types[] = $license['type'];
						main::$cnf['licenses'][] = $license;
					}
				}
			}
		}
		
		if(!is_array(main::$cnf['licenses']) || count(main::$cnf['licenses']) == 0 && $after_update){
			self::error_page('License is not present or has expired. Please visit <a href="http://www.panenthe.com">Panenthe</a> for more information.');
		}
		else
		if(!is_array(main::$cnf['licenses']) || count(main::$cnf['licenses']) == 0 && !$after_update)
		{
			$this->update_license();
		}
		else
		{
			//Set Global Vars
			$vm_limit = 0;
			foreach(main::$cnf['licenses'] AS $license){
				$vm_limit += $license['vms'];
			}
			dev::$tpl->set_constant("licensed_vms",$vm_limit);
			main::$cnf['vm_limit'] = $vm_limit;
		}
		
	}
	
	private function update_license(){
		
		$ip = $_SERVER['SERVER_ADDR'];
		$user = main::$cnf['main']['license_user'];
		$pass = main::$cnf['main']['license_pass'];
		
		$license_url = 
			"http://clients.panenthe.com/".
			"manage/index.php?action=get_license&".
			"username=".urlencode($user)."&".
			"password=".urlencode($pass)."&".
			"ip=".urlencode($ip);
			
		$license_keys = file_get_contents($license_url);
		
		if(trim($license_keys) !== "0"){
			file_put_contents(main::$cnf['main']['root_dir'].'/shared/etc/license',$license_keys);
			$this->validate_license(true);
		}
		else
		{
			self::error_page('License is not present or has expired. Please visit <a href="http://www.panenthe.com">Panenthe</a> for more information.');
		}
		
	}
	
	private function from_62($c)
	{
		# a-z
		if($c >= 36)
			$c += 61;

		# A-Z
		elseif($c >= 10 && $c <= 36)
			$c += 55;

		# 0-9
		elseif($c < 10)
			$c += 48;

		return chr($c);
	}

	private function to_62($c)
	{
		$c = ord($c);

		# 0-9
		if($c >= 48 && $c <= 57)
			$c -= 48;

		# A-Z
		elseif($c >= 65 && $c <= 90)
			$c -= 55;

		# a-z
		elseif($c >= 97 && $c <= 122)
			$c -= 61;

		return $c;
	}

	private function urotate($c, $uamount)
	{
		$n = $this->to_62($c) - $uamount;
		$n = $this->inverse_mod($n, 62);
		$n = $this->from_62($n);

		return $n;
	}

	private function str_urotate($string, $key, $skippers)
	{
		$new_string = null;

		for($i=0; $i<strlen($string); $i++)
		{
			if(in_array($i, $skippers))
				$new_string .= $string{$i};
			else
				$new_string .= $this->urotate($string{$i}, ord($key{$i}));
		}

		return $new_string;
	}

	private function inverse_mod($input, $modulus)
	{
		$out = $input;
		while($out<0)
		{
			$out += $modulus;
		}
		return $out;
	}
	
	private function get_key($c1, $c2, $c3, $c4)
	{
		$chunks = array(
		'Hh*9i', 'JIvbU', 'e_<Z/', 'zaW/t', ']u4{]', '6(&J9',
		'tf{fy', 'a.JKL', 'D`!#Y', '4)O8N', 'g-6|$', 'oxQvr',
		'/2[$j', '^6Eh(', 'sjYxY', ')kesC', 'T,l:7', 'R-|A,',
		'["?wY', 'njs5c', '#!&=w', 'ElvNk', 'f  <\\', 'Y.s8P',
		'3;:sR', 'TI$:>', 'Mdkq-', '{(xPu', 'wS*Ol', '#]"!V',
		'4F!.:', "'55Jg", '?BbfK', '%Sx^0', 'u,n:X', 'Vp[oN',
		'xy95x', '}=]ej', '@BRZ@', '`8R]*', 'IB9j(', '/N/!G',
		'41G.v', 'WC$) ', ')qm{c', 'FiE y', 'AFs)E', 'G&vcQ',
		'wB]<O', 'F<uO!', 'Tk(bC', 'k/*u*', 'M/R|)', '!y82?',
		'h!+y.', '5F"Q5', '`CvJK', 'h$rqY', 'eoq`]', '_o"cw',
		'U*r#Y', '-6<-z'
		);

		return $chunks[$c1].$chunks[$c2].$chunks[$c3].$chunks[$c4];
	}
	
	private function keydec($key)
	{
		# CTVVC-VVVUU-CUUUU-UUCUU
		$key = str_replace('-', null, $key);

		# key characters
		$char_c1 = substr($key, $this->chunk_chars[0], 1);
		$char_c2 = substr($key, $this->chunk_chars[1], 1);
		$char_c3 = substr($key, $this->chunk_chars[2], 1);
		$char_c4 = substr($key, $this->chunk_chars[3], 1);

		# key numbers
		$c1 = $this->to_62($char_c1);
		$c2 = $this->to_62($char_c2);
		$c3 = $this->to_62($char_c3);
		$c4 = $this->to_62($char_c4);

		# get key
		$private_key = $this->get_key($c1, $c2, $c3, $c4);

		# decrypt key
		$dec_license = $this->str_urotate(
			$key, $private_key, $this->chunk_chars
		);

		# get type
		$type = substr($dec_license, 1, 1);

		# get vms
		$vms = intval(
			substr($dec_license, 2, 2).
			substr($dec_license, 5, 3)
		);

		# get expiry
		$date = intval(
			substr($dec_license, 8, 2).
			substr($dec_license, 11, 6).
			substr($dec_license, 18, 2)
		);

		return array(
			'type' => $type,
			'vms' => $vms,
			'date' => $date
		);
	}
	
}

new main($config,$errors);

ob_end_flush();

?>
