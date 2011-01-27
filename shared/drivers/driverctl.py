#!/usr/bin/env python

# Panenthe: driverctl.py
# Defines a command.py class that controls drivers.

import glob
import errors

##

from command import command
import sys
import driver

class driverctl(command):
	def __init__(self, cmd, dict):
		super(driverctl, self).__init__(cmd, dict)

	def execute(self):
		# note: no super()

		# add drivers path to system path for import
		sys.path.append(glob.getfile("drivers", self.driver))

		# import, and make sure it exists
		try:
			exec("import %s_driver" % self.driver)
		except ImportError:
			return errors.throw(errors.DRIVER_NOT_FOUND)

		# check to make sure the method is supported
		try:
			exec("%s_driver.%s_driver.%s" % (
				self.driver, self.driver, self.cmd
			))
		except AttributeError:
			return errors.throw(errors.DRIVER_METHOD_NOT_SUPPORTED)

		# execute method
		exec("self.entity = %s_driver.%s_driver(self.dict)" % \
			(self.driver, self.driver))
		return eval("self.entity.%s()" % self.cmd)
