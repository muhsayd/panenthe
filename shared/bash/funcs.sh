#!/bin/bash

# Panenthe: funcs.sh
# Provides some basic shared functions.

# trim string
function trim(){
	string="$1"
	chars="$2"

	if [ -z "$chars" ]; then
		string="`echo "$string" | sed -r "s/^[ \t]*(.*?[^ \t])[ \t]*$/\1/g"`"
	else
		string="`echo "$string" |
			sed -r "s/^[${chars}]*(.*?[^${chars}])[${chars}]*$/\1/g"`"
	fi
	echo "$string"
}
