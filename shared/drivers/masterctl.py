#!/usr/bin/env python

# Panenthe: masterctl.py
# Defines a command.py class that controls the master server.

import glob
import errors

##

from command import command
import sys
import master

class masterctl(command):
	def __init__(self, cmd, dict):
		super(masterctl, self).__init__(cmd, dict)

	def execute(self):
		self.entity = master.master(self.dict)
		return eval("self.entity.%s()" % self.cmd)
