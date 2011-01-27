<?

function python_escape($code){
	// escape slashes
	$code = str_replace("\\", "\\\\", $code);
	// escape quotes
	$code = str_replace("\"", "\\\"", $code);
	// return code
	return $code;
}

function shell_escape($cmd){
	// escape slashes
	$cmd = str_replace("\\", "\\\\", $cmd);
	// escape quotes
	$cmd = str_replace("\"", "\\\"", $cmd);
	// escape backticks
	$cmd = str_replace("`", "\`", $cmd);
	// return cmd
	return $cmd;
}

function array_to_dict($array){
	// convert $arguments array to python dictionary string
	$first = true;
	$dict = null;
	while(list($key, $val) = each($array)){
		if($first){
			$first = false;
			$dict = '{';
		}
		else{
			$dict .= ', ';
		}

		if(is_bool($val))
			$dict .= "\"{$key}\": " . ($val?'True':'False');
		elseif(is_int($val) || is_double($val))
			$dict .= "\"{$key}\": {$val}";
		elseif(is_array($val)){
			$newdict = array_to_dict($val);
			$dict .= "\"{$key}\": {$newdict}";
		}
		else{
			$val = python_escape($val);
			$dict .= "\"{$key}\": \"{$val}\"";
		}
	}
	$dict .= '}';
	return $dict;
}

function backend_call(
	$class, $command, $arguments, $debug_mode = false,
	$queue_mode = false, $dependency = 0
){

	// TODO: remove?
	$root_dir = '/opt/panenthe';

	// detect invalid input
	if(empty($class) || empty($command) || !is_array($arguments)){
		return '0100';
	}

	// convert to dict
	$dict = array_to_dict($arguments);
	$dict = shell_escape($dict);

	// execute specified command
	$stdout = array();
	$error = null;

	if(!$queue_mode){
		$cmd =
			"sudo {$root_dir}/shared/drivers/backend.py " .
			"{$class} {$command} " .
			($debug_mode?'-d ':null) .
			"\"{$dict}\" 2>&1";

		exec($cmd, $stdout, $error);
	
		if(
			class_exists("main") &&
			isset(main::$cnf['ui_config']['vps_debug']) &&
			main::$cnf['ui_config']['vps_debug'] == 'true'
		){
			dev::output_r($cmd);
		}

	}

	// queue mode
	else{
		$tmp_file = tempnam('/tmp', 'panenthe_q_exec');

		//Setup Dependencies
		$deps = '';
		if(is_array($dependency)){
			foreach($dependency AS $qid){
				$deps .= '-q '.$qid.' ';
			}
		}
		else{
			$deps = '-q '.$dependency;
		}
		
		$cmd =
			"sudo {$root_dir}/shared/drivers/backend.py " .
			"{$class} {$command} " .
			($debug_mode?'-d ':null) .
			($queue_mode?"{$deps} ":null) .
			"\"{$dict}\" &> {$tmp_file} & echo $!";

		exec($cmd, $stdout, $error);

		if(main::$cnf['ui_config']['vps_debug'] == 'true'){
			dev::output_r($cmd);
		}

		// output is PID
		$pid = $stdout[0];

		// wait for process to quit
		$lines = '2';
		while($lines == '2'){
			$stdout = null;

			exec(
				"/usr/bin/env ps -o pid -p {$pid} | /usr/bin/env wc -l",
				$stdout, $error
			);

			// calculate lines, then sleep if necessary
			$lines = $stdout[0];
			if($lines == '2')
				usleep(500);
		}

		// get file contents and then delete file
		$stdout_raw = file_get_contents($tmp_file);
		$stdout = explode("\n", $stdout_raw);
		unlink($tmp_file);

	}

	// dirty hack because OpenVZ, CentOS, and sudo do not play nice
	// TODO: remove this hack when they decide to be friends
	if(isset($stdout[0]) && trim($stdout[0]) == 'audit_log_user_command(): Connection refused')
		$stdout = array_slice($stdout, 1);

	// dirty hack because of remote DNS with SSH causing a ruckus
	if(isset($stdout[0]) && strstr($stdout[0], ' - POSSIBLE BREAK-IN ATTEMPT!') !== false)
		$stdout = array_slice($stdout, 1);

	// TODO: look deeper into this
	// suppress the std tty-in getting thrown by presumably debian
	if(isset($stdout[0]) && trim($stdout[0]) == 'stdin: is not a tty')
		$stdout = array_slice($stdout, 1);

	// TODO:
	/* needs to spit out an error if there is an error code sent by the
	   command, but not defined by the process
	if($stdout[count($stdout)-1]==ERR_SUCCESS && $error!=0){
		$stdout[count($stdout)-1]=BACKEND_GENERAL;
		// do some other stuff?
	}
	*/

	return $stdout;

}

?>
