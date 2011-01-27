#!/bin/bash

# Panenthe: errors.sh
# Throws and decodes error strings.

# input
install_dir="$1"

# load config
source "$install_dir/shared/bash/config.sh"

# errors file
errors_file="$install_dir/shared/etc/errors"

# get error code
function errors_get_code(){
	name="$1"

	# get error code
	error_line="`cat "$errors_file" | grep " | $name | "`"
	code="`echo "$error_line" | cut -d '|' -f1`"
	code="`trim "$code"`"

	# output to caller
	echo "$code"
}

# throw error
function errors_throw(){
	name="$1"
	code="`errors_get_code "$name"`"

	# log
	date="`date +"$config__errors__date_format"`"
	echo "$date:BASH:$code:$name" >> \
		"$install_dir/$config__errors__file_sys_error"

	# output to caller
	echo "$code"
}
