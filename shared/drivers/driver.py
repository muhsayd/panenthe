#!/usr/bin/env python

# Panenthe: driver.py
# Defines an abstract class that controls drivers.

import glob
import errors
from attrdict import attrdict
import server

##

class driver(attrdict):
	def __init__(self, dict):
		super(driver, self).__init__(dict)

		# verify all one fields are present
		if not self.require([
			"driver",
			"server"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# verify all fields are present for server
		if not self.require_dict(self.server, [
			"server_id",
			"parent_server_id",
			"hostname",
			"ip",
			"port",
			"remote_server"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int_dict("server", [
			"server_id",
			"parent_server_id",
			"port"
		])

		# verify all fields are present for remote server
		if not self.require_dict(self.server['remote_server'], [
			"server_id",
			"parent_server_id",
			"hostname",
			"ip",
			"port"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int_dict(["server", "remote_server"], [
			"server_id",
			"parent_server_id",
			"port"
		])

	# private methods {{{

	def __set_sysctl(self, srv, setting, val):
		# set the setting by hand anyway
		srv.do_execute("echo \"%s\" > %s" % (
			val, os.path.join("/proc/sys", setting)
		))

		# see if it exists in sysctl.conf
		(_,stdout,_) = srv.do_execute(
			"/usr/bin/env grep -E \"^[ ]*%s\" %s" % (
				setting, glob.config.get("paths", "sysctl_config")
			)
		)

		# not there, add it
		if "\n".join(stdout).strip() == "":
			srv.do_execute("echo \"%s = %s\" >> %s" % (
				setting, val, glob.config.get("paths", "sysctl_config")
			))

		# it's there, replace current value
		else:
			srv.do_execute(
				"/usr/bin/env sed -r \"s/^[ ]*%s[ ]*=.*$/%s = %s/g\" -i %s" % (
					setting, setting, val,
					glob.config.get("paths", "sysctl_config")
				)
			)

	# }}}


	# system {{{

	def install(self):
		if not self.require("reboot"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_bool("reboot")

	def uninstall(self):
		if not self.require("reboot"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_bool("reboot")

	def activate(self):
		if not self.require("reboot"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_bool("reboot")

	def deactivate(self):
		if not self.require("reboot"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_bool("reboot")

	# }}}

	# control {{{

	def start(self):
		pass

	def stop(self):
		pass

	def restart(self):
		pass

	# }}}

	# maintanance/info {{{

	def status(self):
		pass

	def cleanup(self):
		pass

	# }}}
