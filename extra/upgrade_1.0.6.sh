#!/bin/bash

# Panenthe: liveupdate.sh
# Updates Panenthe incrementally by version.

# input
if [ -z "$1" ]; then
        install_dir="/opt/panenthe"
else
        install_dir="$1"
fi

# download url
download_url="http://www.panenthe.com/release/panenthe_"

# require install_dir
if [ -z "$install_dir" ]; then
	echo "Usage: $0 <install_dir>"
	exit 1
fi

# clear out svn folders
find "$install_dir" -name .svn -print0 | xargs -0 rm -rf

# assign revision
loop_revision="471"

echo
echo "Download new version."
echo

# get version
wget -O "$install_dir/tmp/panenthe_$loop_revision.tar.gz" \
	"$download_url$loop_revision.tar.gz"

echo
echo "Unpack new version."
echo

# extract new version
tar xzmf "$install_dir/tmp/panenthe_$loop_revision.tar.gz" \
	-C "$install_dir"

# success
echo
echo "Updgrade of liveupdate system complete. Starting liveupdate."
echo

# start new liveupdater
chmod +x "$install_dir/scripts/bin/liveupdate.sh"
"$install_dir/scripts/bin/liveupdate.sh" "$install_dir"

echo "Liveupdate complete."
exit 0
