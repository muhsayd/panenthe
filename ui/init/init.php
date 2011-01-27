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

class init{

	public $dev_config;
	public $dev;

	public function __construct($dev_config){
		$this->dev_config = $dev_config;
		$this->append_config();
		$this->process_init();
		$this->load_dataCheck();
	}

	protected function append_config(){

		if(!isset($this->dev_config['tpl']['tpl_path'])){
			$this->dev_config['tpl']['tpl_path'] = '';
		}

		//Append Dev Config
		$this->dev_config['db']['db_enable'] = true;
		$this->dev_config['mail']['mail_enable'] = true;
		$this->dev_config['tpl']['tpl_enable'] = true;
		$this->dev_config['tpl']['tpl_path'] = main::$root.'/'.$this->dev_config['tpl']['tpl_path'];
		$this->dev_config['tpl']['tpl_constants'] = $this->get_tpl_constants();
		$this->dev_config['js']['path'] = main::$uri;
		$this->dev_config['css']['path'] = main::$uri.'/theme/default';

		//Append Main Config
		main::$cnf['db_tables']['data'] = main::$cnf['dev']['db']['db_prefix'].'data';
		main::$cnf['db_tables']['drivers'] = main::$cnf['dev']['db']['db_prefix'].'drivers';
		main::$cnf['db_tables']['events'] = main::$cnf['dev']['db']['db_prefix'].'events';
		main::$cnf['db_tables']['ip_map'] = main::$cnf['dev']['db']['db_prefix'].'ip_map';
		main::$cnf['db_tables']['ip_pools'] = main::$cnf['dev']['db']['db_prefix'].'ip_pools';
		main::$cnf['db_tables']['ip_pools_map'] = main::$cnf['dev']['db']['db_prefix'].'ip_pools_map';
		main::$cnf['db_tables']['ost'] = main::$cnf['dev']['db']['db_prefix'].'ost';
		main::$cnf['db_tables']['plans'] = main::$cnf['dev']['db']['db_prefix'].'plans';
		main::$cnf['db_tables']['servers'] = main::$cnf['dev']['db']['db_prefix'].'servers';
		main::$cnf['db_tables']['server_stats'] = main::$cnf['dev']['db']['db_prefix'].'server_stats';
		main::$cnf['db_tables']['settings'] = main::$cnf['dev']['db']['db_prefix'].'settings';
		main::$cnf['db_tables']['users'] = main::$cnf['dev']['db']['db_prefix'].'users';
		main::$cnf['db_tables']['vps'] = main::$cnf['dev']['db']['db_prefix'].'vps';
		main::$cnf['db_tables']['vps_stats'] = main::$cnf['dev']['db']['db_prefix'].'vps_stats';
		main::$cnf['db_tables']['vps_server_map'] = main::$cnf['dev']['db']['db_prefix'].'vps_server_map';
		main::$cnf['db_tables']['vps_status_history'] = main::$cnf['dev']['db']['db_prefix'].'vps_status_history';
		main::$cnf['db_tables']['vps_user_map'] = main::$cnf['dev']['db']['db_prefix'].'vps_user_map';

	}

	protected function get_tpl_constants(){

		//Get Version
		$version = file_get_contents(main::$cnf['main']['root_dir'].'/shared/etc/version');

		return array(

				//Site Information
				"site_name"			=>	main::$cnf['site_name'],
				"head_title"		=>	main::$cnf['head_title'],
				"logo_url"			=>	main::$cnf['ui_config']['logo_url'],

				//URL Information
				"site_root"			=>	main::$root,
				"site_url"			=>	main::$url,
				"site_uri"			=>	main::$uri,

				//Script Information
				"script_url"		=>	"http://www.panenthe.com",
				"script_name"		=>	"Panenthe",
				"script_slogan"		=>	"VPS Management Solution",
				"script_version"	=>	$version,
				"script_site"		=>	"panenthe.com",

				//Framework Information
				"framework_url"		=>	"http://framework.panenthe.com",
				"framework_name"	=>	"Panenthe Framework",
				"framework_version"	=>	"1.0.0",
				"framework_site"	=>	"framework.panenthe.com",
				"copyright_brand"	=>	date("Y")." Panenthe",

				//Header Date
				"header_date_month"	=>	date("M"),
				"header_date_day"	=>	date("d"),
				"header_date_year"	=>	date("Y"),
				"header_time_hour"	=>	date("g"),
				"header_time_minute"=>	date("i"),
				"header_time_period"=>	date("a"),

		);
	}

	protected function process_init(){

		$this->dev = new dev();

		//Set Config
		$this->dev->dev_config($this->dev_config);

		//Init Dev
		$this->dev->init();

	}

	protected function load_dataCheck(){
		require_once(dev::$root.'/plugins/dev_datacheck.php');
	}

}

?>
