#!/bin/bash

install_dir="$1"

# restart services
"$install_dir/srv/bin/pan_httpd" restart
"$install_dir/srv/bin/pan_mysqld" restart

# update database
"${install_dir}/srv/bin/php" "${install_dir}/shared/bridge/db_update.php" \
update_db "${install_dir}/extra/db/delta/panenthe_1.5.2_delta.sql"
