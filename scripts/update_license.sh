#!/bin/bash

# variables
PANENTHE_CONFIG="/opt/panenthe/shared/etc/panenthe.conf"

# check Panenthe username/password
validate=x
while [ "$validate" != "1" ]; do
	if [ "$validate" != "x" ]; then
		echo
		echo "Invalid password.  Please type your password in again."
		echo
	fi

	echo
	username=""
	while [ -z "$username" ]; do
		read -p "Username: " username
	done

	password=""
	while [ -z "$password" ]; do
		read -s -p "Password: " password
	done

	validate="`wget -q -O /dev/stdout \
		"https://clients.panenthe.com/manage/index.php?action=validate_user" \
		--no-check-certificate \
		--post-data="username=$username&password=$password"`"
done

# store username/password
sed -r "s/^[ ]*license_user[ ]*=/license_user = $username/g" -i $PANENTHE_CONFIG
sed -r "s/^[ ]*license_pass[ ]*=/license_pass = $password/g" -i $PANENTHE_CONFIG
