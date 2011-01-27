#!/usr/bin/env python

# update script for 1.0.4

import copy
import sys
import urllib

##

import glob
import errors

from backend import backend
from execute import *
import api
import vps

# get server list
ac = api.api_call("server_list", {})
ret = ac.execute()
if ret != errors.ERR_SUCCESS:
	sys.exit(1)
servers = ac.output()

# for each server, call bandwidth initialize
for server in servers:
	# get the dictionary
	server_text = urllib.unquote(str(server)).replace("+", " ")
	server_dict = eval(server_text)
	server_dict['remote_server'] = copy.copy(server_dict)
	server_text = str(server_dict)

	# create bandwidth cron on master, and that's all
	if server['parent_server_id'] == 0:
		print backend.execute(
			"masterctl", "initialize_bw_cron", server_text, True
		)
		continue

	# initialize bandwidth on the server
	print backend.execute("serverctl", "initialize_bw", server_text, True)
