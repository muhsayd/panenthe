#!/bin/bash

# Panenthe: config.sh
# Reads and parses the configuration file.

# input
install_dir="$1"

# load libraries
source "$install_dir/shared/bash/funcs.sh"

# loader
function load_config_file(){
	file="$1"

	# loop and parse out each line
	cursection=""
	while read line; do
		# cleanup
		line="`trim "$line"`"

		# skip empty or commented lines
		if [ -z "$line" ] || [ "${line:0:1}" = "#" ]; then
			continue
		fi

		# new section
		if [ "${line:0:1}" = "[" ] && [ "${line:${#line}-1:1}" = "]" ]; then
			cursection="${line:1:${#line}-2}"

		# new config item
		else
			name="`echo "$line" | sed -r "s/^([^=]*)=.*$/\1/g"`"
			name="`trim "$name"`"
			value="`echo "$line" | sed -r "s/^[^=]*=(.*)$/\1/g"`"
			value="`trim "$value"`"
			value="`trim "$value" '"'`"
			value="`echo "$value" | sed "s/%%/%/g"`"

			# this is not needed i dont believe
			#if [ ! -z "$cursection" ] && [ -z "$name" ] && [ -z "$value" ]; then
				eval "config__${cursection}__${name}=\"${value}\""
			#fi
		fi

	done < "$file"
}

# load config files
load_config_file "$1/shared/etc/sys.conf"
load_config_file "$1/shared/etc/panenthe.conf"
