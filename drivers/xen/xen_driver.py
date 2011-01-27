#!/usr/bin/env python

# Panenthe: xen_driver.py
# Xen driver controller.

import glob
import errors

##

import server
from driver import driver
from execute import *

import xen_exit_codes

class xen_driver(driver):

	def __init__(self, dict):
		super(xen_driver, self).__init__(dict)

	# system {{{

	def activate(self):
		ret = super(xen_driver, self).activate()
		if ret: return ret

		# get server object to work with
		srv = server.server(self.server)

		# get the kernel number from the grub config
		(_,stdout,_) = srv.do_execute(
			"/usr/bin/env grep -E \"^[ ]*title\" %s | " %
				glob.config.get("paths", "grub_config") +
			"/usr/bin/env grep -B 1000 xen | /usr/bin/env wc -l"
		)

		grub_number = int(stdout[0])-1

		# set the xen to boot as the default kernel
		srv.do_execute(
			"/usr/bin/env sed -r \"s/^[ ]*default[ ]*=.*$/default=%s/g\" " %
				grub_number +
			"-i %s" % glob.config.get("paths", "grub_config")
		)

		# reboot if necessary
		if self.reboot:
			# backgrounded, just in case it matters
			srv.do_execute("%s -r now &" % glob.config.get("paths", "shutdown"))

		return errors.throw(errors.ERR_SUCCESS)

	def deactivate(self):
		ret = super(xen_driver, self).deactivate()
		if ret: return ret
		return errors.BACKEND_NOT_DEFINED_YET

	def __installed(self, srv):
		# check to make sure it's not already a Xen kernel
		(exit_code,stdout,_) = srv.do_execute("uname -r")

		if exit_code != 0:
			return errors.throw(errors.DRIVER_INSTALL_FAIL)

		# search for xen on kernel name
		search = stdout[0].find("xen")

		# already installed
		if search != -1:
			return errors.throw(errors.DRIVER_ALREADY_INSTALLED)

	def install(self):
		ret = super(xen_driver, self).install()
		if ret: return ret

		# get server object to work with
		srv = server.server(self.server)

		# see if it's already installed
		ret = self.__installed(srv)
		if ret == errors.DRIVER_ALREADY_INSTALLED:
			return ret

		# get distro
		distro = srv.get_remote_distro()

		if distro == "redhat":
			# get YUM package
			(exit_code,_,_) = srv.do_execute(
				"yum -y install xen xen-libs kernel-xen"
			)

			if exit_code != 0:
				return errors.throw(errors.DRIVER_INSTALL_FAIL)

			return errors.throw(errors.ERR_SUCCESS)

		return errors.throw(errors.DRIVER_OS_NOT_SUPPORTED)

	def uninstall(self):
		ret = super(xen_driver, self).uninstall()
		if ret: return ret
		return errors.BACKEND_NOT_DEFINED_YET

	# }}}

	# control {{{

	def start(self):
		ret = super(xen_driver, self).start()
		if ret: return ret

		(exit_code,_,_) = execute_drv(self,
			"%s start" % os.path.join(
				glob.config.get("paths", "initd"), "xend"
			)
		)

		if exit_code != 0:
			return errors.throw(errors.DRIVER_START_FAIL)

		return errors.throw(errors.ERR_SUCCESS)

	def stop(self):
		ret = super(xen_driver, self).stop()
		if ret: return ret

		(exit_code,_,_) = execute_drv(self,
			"%s stop" % os.path.join(
				glob.config.get("paths", "initd"), "xend"
			)
		)

		if exit_code != 0:
			return errors.throw(errors.DRIVER_STOP_FAIL)

		return errors.throw(errors.ERR_SUCCESS)

	def restart(self):
		ret = super(xen_driver, self).restart()
		if ret: return ret

		(exit_code,_,_) = execute_drv(self,
			"%s restart" % os.path.join(
				glob.config.get("paths", "initd"), "xend"
			)
		)

		if exit_code != 0:
			return errors.throw(errors.DRIVER_RESTART_FAIL)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# maintanance/info {{{

	def status(self):
		ret = super(xen_driver, self).status()
		if ret: return ret

		(exit_code,stdout,_) = execute_drv(self,
			"%s status" % os.path.join(
				glob.config.get("paths", "initd"), "xend"
			)
		)

		stdout_str = "\n".join(stdout)

		# Xen is not installed (or at least /etc/init.d/xend doesn't exist)
		# this number was tested on both CentOS and Debian
		if exit_code == 32512:
			return errors.throw(errors.DRIVER_STATUS_NOT_INSTALLED)

		# Xen is installed or something is wonky
		else:
			# not a valid kernel
			if stdout_str.strip() == "":
				return errors.throw(errors.DRIVER_STATUS_NOT_ACTIVATED)

			# check if running
			try:
				stdout_str.index("running")
				return errors.throw(errors.DRIVER_STATUS_STARTED)

			except ValueError:
				# stopped
				try:
					stdout_str.index("stopped")
					return errors.throw(errors.DRIVER_STATUS_STOPPED)

				# something wonky
				except ValueError:
					return errors.throw(errors.DRIVER_STATUS_FAIL)

	def cleanup(self):
		ret = super(xen_driver, self).cleanup()
		if ret: return ret
		return errors.BACKEND_NOT_DEFINED_YET

	# }}}
