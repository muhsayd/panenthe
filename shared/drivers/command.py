#!/usr/bin/env python

# Panenthe: command.py
# Defines an abstract command line class called by backend.py.

import glob
from attrdict import attrdict

##

class command(attrdict):
	def __init__(self, cmd, dict):
		super(command, self).__init__(dict)
		self.cmd = cmd

	def execute(self):
		return eval("self.%s()" % self.cmd)
