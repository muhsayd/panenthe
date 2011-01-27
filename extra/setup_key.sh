#/bin/bash

$ip = $1
$port = $2
$password = $3

function call_panenthe(){
        python /opt/panenthe/shared/drivers/backend.py "$1" "$2" "$3"
}

function call_panenthe_debug(){
        python /opt/panenthe/shared/drivers/backend.py "$1" "$2" -d "$3"
}

call_panenthe_debug masterctl install_key \
"{'server_id': 1, 'parent_server_id': 0, 'hostname': 'panenthevm', "\
"'ip': '10.0.0.1', 'port': 7160, 'remote_server': {"\
"'server_id': 2, 'parent_server_id': 1, 'hostname': 'panenthe.slave', "\
"'ip': '$ip', 'port': $port, 'password': '$password'}}"

