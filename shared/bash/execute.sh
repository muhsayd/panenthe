#!/bin/bash

# Panenthe: execute.sh
# Wrapper to execute a command and log it into the appropriate files.

# input
install_dir="$1"

# libraries
source "$install_dir/shared/bash/config.sh"

# setup (probably unnecessary but whatever)
log_stdout=""
log_stderr=""
log_exit_code=""
log_start_date=""

# log the command execution
function do_log_start(){
	command="$1"
	log_start_date="`date +"$config__errors__date_format"`"
}

# log the stdout and stderr
function do_log_end(){
	command="$1"
	start_date="$2"
	stdout="$3"
	stderr="$4"
	exit_code="$5"

	# get date
	end_date="`date +"$config__errors__date_format"`"

	# log everything
	echo "$start_date:BASH:STARTCMD:$command" >> \
		"$install_dir/$config__logging__file_log"
	echo "$stdout" >> "$install_dir/$config__logging__file_log"
	echo "$end_date:BASH:ENDCMD:$exit_code:$command" >> \
		"$install_dir/$config__logging__file_log"

	# log stderr if necessary
	if [ ! -z "$stderr" ]; then
		echo "$start_date:BASH:STARTCMD:$command" >> \
			"$install_dir/$config__errors__file_error"
		echo "$stderr" >> "$install_dir/$config__errors__file_error"
		echo "$end_date:BASH:ENDCMD:$exit_code:$command" >> \
			"$install_dir/$config__errors__file_error"
	fi
}

# main execution command
function log_execute(){
	command="$1"
	log_stdout=""
	log_stderr=""

	file_stdout="/tmp/$$.file_stdout"
	file_stderr="/tmp/$$.file_stderr"

	# begin log
	do_log_start "$command"

	# execute command
	eval "$command" 1> "$file_stdout" 2> "$file_stderr"

	# sort output
	log_exit_code="$?"
	log_stdout="`cat "$file_stdout"`"
	log_stderr="`cat "$file_stderr"`"

	# finish log
	do_log_end "$command" "$log_start_date" \
		"$log_stdout" "$log_stderr" "$log_exit_code"

	# cleanup
	rm "$file_stdout"
	rm "$file_stderr"
}

# return stdout
function log_get_stdout(){
	echo "$log_stdout"
}

# return stderr
function log_get_stderr(){
	echo "$log_stderr"
}

# return exit code
function log_get_exit_code(){
	echo "$log_exit_code"
}
