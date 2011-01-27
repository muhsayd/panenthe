#!/usr/bin/env python

# Panenthe: serverctl.py
# Defines a command.py class that controls physical servers.

import glob
import errors

##

from command import command
import sys
import server

class serverctl(command):
	def __init__(self, cmd, dict):
		super(serverctl, self).__init__(cmd, dict)

	def execute(self):
		self.entity = server.server(self.dict)
		return eval("self.entity.%s()" % self.cmd)
