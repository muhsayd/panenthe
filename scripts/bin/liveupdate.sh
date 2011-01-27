#!/bin/bash

# Panenthe: liveupdate.sh
# Updates Panenthe incrementally by version.

# input
install_dir="$1"

# head_release
head_release_url="http://www.panenthe.com/release/releases"

#download url
download_url="http://www.panenthe.com/release/panenthe_"

# require install_dir
if [ -z "$install_dir" ]; then
	echo "Usage: $0 <install_dir>"
	exit 1
fi

# load libraries
source "$install_dir/shared/bash/errors.sh"
source "$install_dir/shared/bash/execute.sh"

# function to fix the file perms for all of panenthe
function file_perms(){

	# clear tmp
	rm -rf "$install_dir/tmp"
	mkdir -p "$install_dir/tmp"

	# fix permissions if root called all the SVN stuff
	chown -R panenthe.panenthe "$install_dir"
	find "$install_dir" -type f -print0 | xargs -0 chmod 660
	find "$install_dir" -type d -print0 | xargs -0 chmod 770
	find "$install_dir" -name \.svn -prune -o -type d -name bin -print0 | \
		xargs -0 chmod -R 770
	find "$install_dir" -name \.svn -prune -o -type d -name libexec -print0 | \
		xargs -0 chmod -R 770
	find "$install_dir" -type d -print0 | xargs -0 chmod g+s

	# make certain scripts executable when not in a "bin" or "libexec" directory
	chmod 770 "$install_dir/shared/drivers/backend.py"
	chmod 770 "$install_dir/shared/drivers/panenthe_q.py"

	# clear out any cached .pyc files
	find "$install_dir" -name \.svn -prune -o -type f -name "*.pyc" -delete

}

# update perms before we start
file_perms

# get current revision number
current_release="`cat \"$install_dir/shared/etc/version\" | \
	sed -r \"s/^.*\.([0-9]+)$/\1/\"`"

# get head revision number
rm -rf "$install_dir/tmp"
mkdir -p "$install_dir/tmp"
chmod 770 "$install_dir/tmp"
wget "$head_release_url" -O "$install_dir/tmp/releases"

# loop through new revisions
while read release; do

	if [ "$current_release" -lt "$release" ]; then

		# assign release for legacy
		loop_revision="$release"

		# get version
		wget -O "$install_dir/tmp/panenthe_$loop_revision.tar.gz" \
			"$download_url$loop_revision.tar.gz"

		# remove update scripts
		rm -rf "${install_dir}/scripts/bin/update-"*

		# extract new version
		tar xzmf "$install_dir/tmp/panenthe_$loop_revision.tar.gz" \
			-C "$install_dir"

		# restart services (in case they changed... also because we probably
		# screwed up their sockets with the above commands)
		"$install_dir/srv/bin/pan_httpd" restart
		"$install_dir/srv/bin/pan_mysqld" restart

		# execute updater scripts
		find "${install_dir}/scripts/bin" -name update-\*.sh | \
		while read script_name; do
			log_execute "bash \"${script_name}\" \"${install_dir}\""
		done

		# fail gracefully
		stderr="`log_get_stderr`"
		if [ ! -z "$stderr" ]; then

			# throw update error
			errors_throw LIVEUP_REV_UPDATE
			exit 1
		fi

	fi

done < "$install_dir/tmp/releases"

# update again
file_perms

# restart services (in case they changed... also because we probably screwed
# up their sockets with the above commands)
# "$install_dir/srv/bin/pan_httpd" restart
# "$install_dir/srv/bin/pan_mysqld" restart

# success
echo "0000"
exit 0
