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

require_once('vw/vw_welcomeemail.php');
require_once(main::$root.'/apps/vps/func/func_vps.php');

class idx_welcomeemail{

	private $action_message = '';

    public function __construct(){
    	main::check_permissions('welcomeemail');
		$this->page_header();
		$this->action_message();
		$this->test_email();
		$this->post();
		$this->show_page();
		$this->page_footer();
	}
	
	private function test_email(){
		
		if(isset(dev::$post['action']) && dev::$post['action'] = 'test_email'){
			
			$tags = array(
				"site_url"		=>	main::$cnf['login_url'],
				"site_name"		=>	main::$cnf['site_name'],
				"hostname"		=>	'test.vps.com',
				"name"			=>	'Test VPS',
				"first_name"	=>	'Test',
				"last_name"		=>	'User',
				"username"		=>	'test123',
				"email"			=>	dev::$post['email'],
				"password"		=>	'test123',
				"ip_address"	=>	'192.168.1.1',
				"root_password"	=>	'test123',
				"disk_space"	=>	'30000',
				"backup_space"	=>	'0',
				"swap_space"	=>	'0',
				"g_mem"			=>	'512',
				"b_mem"			=>	'512',
				"cpu_pct"		=>	'100',
				"cpu_num"		=>	'1',
				"out_bw"		=>	'15',
				"in_bw"			=>	'15',
				"ost"			=>	'CentOS 5.3 x86'
			);
			
			$message = func_vps::parse_welcome_email($tags);
			
			dev::$mail->sendMail(array(
				"To"		=>	dev::$post['email'],
				"Subject"	=>	"Welcome: ".$tags['hostname']." has been created!",
				"Message"	=>	$message
			));
			
			main::set_action_message("Test welcome email has been sent.");
			
		}

	}

	private function post(){

		if(isset(dev::$post['action']) && dev::$post['action'] == 'do_update'){
			//Check for Row
			$query = dev::$db->query("
				SELECT * FROM ".main::$cnf['db_tables']['data']."
				WHERE data_id = '".dev::$post['data_id']."' 
			");
			
			if($query->rowCount() > 0){	
				dev::$db->exec("
					UPDATE ".main::$cnf['db_tables']['data']." 
					SET value = '".dev::$post['welcome_email']."' 
					WHERE data_id = '".dev::$post['data_id']."' 
					LIMIT 1
				");
			}
			else
			{
				//Insert
				dev::$db->exec("
					INSERT INTO ".main::$cnf['db_tables']['data']."
					(
						name,
						value,
						active
					)
					VALUES
					(
						'welcome_email',
						'".dev::$s_post['welcome_email']."',
						'1'
					)
				");
			}

			//Log Event
			event_api::add_event("Update welcome email with a new template.");

			main::set_action_message("Welcome email has been updated.");
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

	private function get_email_template(){
		
		$query = dev::$db->query("
			SELECT * FROM ".main::$cnf['db_tables']['data']."
			WHERE name = 'welcome_email'
			ORDER BY data_id DESC
			LIMIT 1
		");
		
		$row = $query->fetch();
		return $row;
			
	}

	private function show_page(){
		$email_template = $this->get_email_template();
		vw_welcomeemail::welcome_email(
			$email_template['data_id'],
			$email_template['value']
		);
	}

}

?>
