#!/usr/bin/env python

# Panenthe: vpsctl.py
# Defines a command.py class that controls VPSes.

import glob
import errors

##

from command import command
import sys

class vpsctl(command):
	def __init__(self, cmd, dict):
		super(vpsctl, self).__init__(cmd, dict)

		if not self.require([
			"driver"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def execute(self):
		# note: no super()

		# add drivers path to system path for import
		sys.path.append(glob.getfile("drivers", self.driver))

		# import, and make sure it exists
		try:
			exec("import %s_core" % self.driver)
		except ImportError:
			return errors.throw(errors.DRIVER_NOT_FOUND)

		# check to make sure the method is supported
		try:
			exec("%s_core.%s_core.%s" % (self.driver, self.driver, self.cmd))
		except AttributeError:
			return errors.throw(errors.DRIVER_METHOD_NOT_SUPPORTED)

		# execute method
		exec("self.entity = %s_core.%s_core(self.dict)" % \
			(self.driver, self.driver))
		return eval("self.entity.%s()" % self.cmd)
