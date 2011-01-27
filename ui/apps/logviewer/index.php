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

require_once('vw/vw_logviewer.php');

class idx_logviewer{

	private $action_message = '';

    public function __construct(){
    	main::check_permissions('logviewer');
		$this->page_header();
		$this->show_page();
		$this->page_footer();
    }

	private function page_header(){
		main::page_header();
	}

	private function page_footer(){
		main::page_footer();
	}
	
	private function readLastLines($file,$lines=100){
		
		$read_lines = '';
		$ln = 0;
		$chunk_size = 4096;
		
		$fh = fopen($file,'r');
		//Go To end of the file
		fseek($fh,0,SEEK_END);
		$current_pos = filesize($file);
		
		while($ln < $lines){
			
			$current_pos -= $chunk_size;
			fseek($fh,$current_pos);
			$chunk = fread($fh, $chunk_size);
			
			//Count New Lines in Chunk
			$line_count = explode("\n",$chunk);
			$ln += count($line_count);
			
			$read_lines .= $chunk;
			
		}
		
		return $read_lines;
	
	}
	
	private function get_logs(){
		
		$logs = array();
		$logs['error'] = $this->readLastLines(main::$cnf['main']['root_dir'].'/logs/error');
		$logs['action'] = $this->readLastLines(main::$cnf['main']['root_dir'].'/logs/action');
		
		return $logs;
		
	}

	private function show_page(){
		
		$logs = $this->get_logs();
		vw_logviewer::view_logs($logs);
		
	}

}

?>
