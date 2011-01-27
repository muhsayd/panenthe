#!/bin/bash

install_dir="$1"

# save iptables counters if redhat
if [ -e "/etc/redhat-release" ]; then
	sed -r \
		"s/^[ ]*IPTABLES_SAVE_COUNTER[ ]*=.*$/IPTABLES_SAVE_COUNTER=\"yes\"/g" \
		-i /etc/init.d/iptables
fi

# update database
"${install_dir}/srv/bin/php" "${install_dir}/shared/bridge/db_update.php" \
update_db "${install_dir}/extra/db/delta/panenthe_1.0.5_delta.sql"

# update for python
PYTHONPATH="${install_dir}/shared/drivers" \
/usr/bin/env python \
"${install_dir}/scripts/bin/update-1.0.5.py" "${install_dir}"
