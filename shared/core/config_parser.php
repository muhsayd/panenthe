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

function parse_config($data){
	
	$config = array();

	$lines = explode("\n",$data);
	
	foreach($lines AS $line){
		$line = trim($line);
		if(!empty($line)){

			// commented line
			if($line{0} == '#')
				continue;

			// section heading
			elseif($line{0} == '[' && $line{strlen($line)- 1} == ']'){
				$section = substr($line,1,(strlen($line) - 2));
			}

			// name/value pairs
			else
			{
				$eq = strpos('=',$line);
				$nv = array(
					'0'	=>	substr($line,0,($eq - 1)),
					'1' =>	substr($line,($eq + 1),strlen($line))
				);
				$nv = explode('=',$line);
				foreach($nv AS $key => $value){
					$nv[$key] = trim(str_replace('"','',str_replace('\'','',$value)));
				}
				if(isset($nv[0]) && isset($nv[1])){
					$config[$section][$nv[0]] = $nv[1];
				}
				else
				if(isset($nv[0])){
					$config[$section][$nv[0]] = '';
				}
			}
		}
	}

	return $config;

}

$config = array();

//Load User Config
$user_config = file_get_contents('/etc/panenthe.conf');

//Parse User Config
$config = parse_config($user_config);
define('PANENTHE_ROOT',$config['main']['root_dir']);


//Load Sys Config
$sys_config = file_get_contents(PANENTHE_ROOT.'/shared/etc/sys.conf');
$config = parse_config($sys_config);

//Overwrite with User Config
$user_config_parsed = parse_config($user_config);

foreach($config AS $section => $vars){
	if(isset($user_config_parsed[$section])){
		$config[$section] = array_merge($config[$section],$user_config_parsed[$section]);
	}
}

foreach($user_config_parsed AS $section => $vars){
	if(!isset($config[$section])){
		$config[$section] = $user_config_parsed[$section];
	}
}

//Garbage Collect
unset($user_config,$sys_config,$user_config_parsed);

?>
