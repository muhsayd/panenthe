#!/usr/bin/env python

# Panenthe: ovz_core.py
# OpenVZ driver core.

import glob
import errors

##

import os
import re

from vps import vps
from execute import *
import php

import ovz_exit_codes
import php_exit_codes

class ovz_core(vps):

	def __init__(self, dict):
		super(ovz_core, self).__init__(dict)

		# cast real_id as int if available
		try:
			self.real_id
			self.force_cast_int("real_id")
		except AttributeError: pass

	# static methods {{{

	@staticmethod
	def get_new_real_id(vps):
		# calculate new real_id
		real_id = 100
		exit_code = 0

		while exit_code == 0:
			# check if real_id has storage used
			(exit_code,_,_) = vps.do_execute("test -d /vz/private/%d" % real_id)

			# if so, increment and continue
			if exit_code == 0:
				real_id += 10

		# create the directory so that the next next_id will not generate the
		# same number (useful since the queue can queue up two creates at once)
		(_,_,_) = vps.do_execute("mkdir /vz/private/%d" % real_id)

		return real_id

	# }}}

	# private methods {{{

	# strip tar.gz if necessary from OS image
	def get_ost_name(self):
		if self.ost[-7:] == ".tar.gz":
			return self.ost[:-7]
		else:
			return self.ost

	# }}}

	# meta {{{

	def next_id(self):
		ret = super(ovz_core, self).next_id()
		if ret: return ret

		# real_id is fine
		try:
			self.real_id

		# need new real_id
		except AttributeError:
			ret = ovz_core.get_new_real_id(self)

			# fail
			if not type(ret) == int:
				return errors.throw(errors.OVZ_GENERATE_REAL_ID)

			# success
			self.real_id = ret

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"vps", "update_real_id",
			str(self.vps_id), str(self.real_id)
		)

		return php_exit_codes.translate(php_exit_code)

	# }}}

	# database {{{

	def lock(self):
		ret = super(ovz_core, self).lock()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	def unlock(self):
		ret = super(ovz_core, self).unlock()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# management {{{

	def create(self):
		ret = super(ovz_core, self).create()
		if ret: return ret

		# copy OS template if necessary
		if self.server_is_slave():

			# sanitize OST path
			self.do_execute("mkdir -p %s" %
				glob.config.get("paths", "ost_ovz")
			)

			# get OST path
			ost_path = os.path.join(
				glob.config.get("paths", "ost_ovz"),
				executer.escape(self.ost)
			)

			# sync OST file
			server = self.get_server()
			server.file_sync(ost_path)

			# OST directory exists
			(exit_code,_,_) = self.do_execute(
				"test -e /vz/template/cache"
			)

			# exists, get rid of it
			if exit_code == 0:

				# check for OST directory symlink
				(exit_code,_,_) = self.do_execute(
					"test -L /vz/template/cache"
				)

				# is a symlink, remove it
				if exit_code == 0:
					self.do_execute("rm -f /vz/template/cache")

				# not a symlink? fix it
				else:

					# check if destination exists
					(exit_code,_,_) = self.do_execute(
						"test -e /vz/template/__cache"
					)

					# if it does, remove it
					if exit_code == 0:
						self.do_execute("rm -rf /vz/template/__cache")

					# move to new directory
					self.do_execute(
						"mv /vz/template/cache /vz/template/__cache"
					)

			# make symlink
			self.do_execute(
				"ln -s %s /vz/template/cache" %
					glob.config.get("paths", "ost_ovz")
			)

		# get ost name
		ost_name = self.get_ost_name()

		# delete default config
		self.do_execute(
			"rm -f %s" % glob.config.get("paths", "vzconf_default")
		)

		# copy default config over
		execute(
			"scp -i %s -P %d %s root@%s:%s" % (
				glob.config.get("paths", "master_private_key"),
				self.server['port'],
				glob.getfile("shared/etc/ovz_vm_default.conf"),
				executer.escape(self.server['ip']),
				glob.config.get("paths", "vzconf_default")
			)
		)

		"""
		# check if disk quotas are enabled
		(exit_code,stdout,_) = self.do_execute(
			"/usr/bin/env grep DISK_QUOTA %s" %
				glob.config.get("paths", "vzconf")
		)

		# disk quotas not set
		if exit_code != 0:
			disk_quotas = None

		else:
			# disk quotas not set
			stdout_str = "\n".join(stdout).strip()
			if stdout_str == "":
				disk_quotas = None

			# disk quotas set to yes
			elif stdout_str.lower().find("yes"):
				disk_quotas = True

			# disk quotas set to no
			else:
				disk_quotas = False

		# disable disk quotas for create if enabled
		if disk_quotas == True:
			self.do_execute(
				"/usr/bin/env sed -r " +
				"\"s/^[ ]*DISK_QUOTA[ ]*=.*$/DISK_QUOTA=no/g\" -i %s" %
					glob.config.get("paths", "vzconf")
			)

		# make sure disk quotas are disabled if set as default
		elif disk_quotas == None:
			self.do_execute("echo \"DISK_QUOTA=no\" >> %s" %
				glob.config.get("paths", "vzconf")
			)

		# else: it's already disabled...

		"""

		# remove directory if it's empty; goes with get_new_real_id()
		(exit_code,_,_) = self.do_execute("rmdir /vz/private/%d" % self.real_id)

		# create
		(exit_code,_,_) = self.do_execute( # prolog anyone?
			"%s create %d --ostemplate %s --hostname %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				executer.escape(ost_name),
				executer.escape(self.hostname)
			)
		)

		"""
		# re-enable disk quotas if necessary
		if disk_quotas == True:
			self.do_execute(
				"/usr/bin/env sed -r " +
				"\"s/^[ ]*DISK_QUOTA[ ]*=.*$/DISK_QUOTA=yes/g\" -i %s" %
					glob.config.get("paths", "vzconf")
			)

		# delete line if it wasn't there before
		elif disk_quotas == None:
			self.do_execute("/usr/bin/env sed \"/DISK_QUOTA/d\" -i %s" %
				glob.config.get("paths", "vzconf")
			)
		"""

		# fail
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# continue setting stuff
		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --setmod restart --quotaugidlimit 3000 " % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			) + \
			"--cpulimit %d --cpus %d " % (
				self.cpu_pct,
				self.cpu_num
			) + \
			# http://www.linux.com/archive/feature/114214
			"--vmguarpages %d --privvmpages %d --diskspace %dK" % (
				(self.g_mem*256/1024), (self.b_mem*256/1024), self.disk_space
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def destroy(self):
		ret = super(ovz_core, self).destroy()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s destroy %d" % (glob.config.get("paths", "vzctl"), self.real_id)
		)

		return ovz_exit_codes.translate(exit_code)

	def modify(self):
		ret = super(ovz_core, self).modify()
		if ret: return ret

		# continue setting stuff
		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --hostname %s --cpulimit %d --cpus %d " % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				executer.escape(self.hostname),
				self.cpu_pct, self.cpu_num
			) + \
			# http://www.linux.com/archive/feature/114214
			"--vmguarpages %d --privvmpages %d --diskspace %dK" % (
				(self.g_mem*256/1024), (self.b_mem*256/1024), self.disk_space
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def ost_create(self):
		ret = super(ovz_core, self).ost_create()
		if ret: return ret

		# stop VM
		(exit_code,_,_) = self.do_execute(
			"%s stop %d" % (
				glob.config.get("paths", "vzctl"), self.real_id
			)
		)

		# also must account for not running
		ret = ovz_exit_codes.translate(exit_code)
		if exit_code != 0 and ret != errors.OVZ_NOT_RUNNING:
			return ret

		# sanitize OST path
		self.do_execute("mkdir -p \"%s\"" % glob.config.get("paths", "ost_ovz"))

		# get OST path and private directories
		ost_path = os.path.join(
			glob.config.get("paths", "ost_ovz"), executer.escape(self.ost_file)
		)
		ost_private = os.path.join(
			glob.config.get("paths", "ovz_private"),
			str(self.real_id)
		)

		# sanity checks
		self.do_execute(
			"rm -f \"%s\" \"%s\"" % (ost_path + ".tar", ost_path + ".tar.gz")
		)

		# get list of directories, then tar it
		(exit_code,_,_) = self.do_execute(
			"ls -x \"%s\" | /usr/bin/env xargs " %
				ost_private +
			"/usr/bin/env tar cf \"%s\" -C \"%s\"" % (
				ost_path + ".tar", ost_private
			)
		)

		if exit_code != 0:
			return errors.throw(ERR_TAR_FAILED)

		# gzip tarred VM
		(exit_code,_,_) = self.do_execute(
			"/usr/bin/env gzip \"%s\"" % (ost_path + ".tar")
		)

		if exit_code != 0:
			return errors.throw(ERR_GZIP_FAILED)

		# transfer OST over to master node
		(exit_code,_,_) = execute(
			"scp -i \"%s\" -P %d \"root@%s:%s\" \"%s\"" % (
				glob.config.get("paths", "master_private_key"),
				self.server['port'],
				executer.escape(self.server['ip']),
				ost_path + ".tar.gz", ost_path + ".tar.gz"
			)
		)

		if exit_code != 0:
			return errors.throw(ERR_SCP_FAILED)

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"ost", "insert",
			self.ost_name,
			ost_path + ".tar.gz",
			self.ost_driver_id, self.ost_arch
		)

		if php_exit_code != 0:
			return php_exit_codes.translate(php_exit_code)

		# start VM
		(exit_code,_,_) = self.do_execute(
			"%s start %d" % (
				glob.config.get("paths", "vzctl"), self.real_id
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def rebuild(self):
		ret = super(ovz_core, self).rebuild()
		if ret: return ret

		# call destroy
		error_code = self.destroy()
		if error_code != errors.ERR_SUCCESS:
			return error_code

		# call create
		error_code = self.create()
		if error_code != errors.ERR_SUCCESS:
			return error_code

		return error_code

	def suspend_ip(self):
		ret = super(ovz_core, self).suspend_ip()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	def unsuspend_ip(self):
		ret = super(ovz_core, self).unsuspend_ip()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# control {{{

	def poweroff(self):
		ret = super(ovz_core, self).poweroff()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s exec %d %s -h now" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				glob.config.get("paths", "shutdown")
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def reboot(self):
		ret = super(ovz_core, self).reboot()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s restart %d" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"vps", "is_running",
			str(self.server['server_id']), str(self.vps_id),
			"1"
		)

		# just report error, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)

		return ovz_exit_codes.translate(exit_code)

	def start(self):
		ret = super(ovz_core, self).start()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s start %d" % (glob.config.get("paths", "vzctl"), self.real_id)
		)

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"vps", "is_running",
			str(self.server['server_id']), str(self.vps_id),
			"1"
		)

		# just report error, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)

		return ovz_exit_codes.translate(exit_code)

	def stop(self):
		ret = super(ovz_core, self).stop()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s stop %d" % (glob.config.get("paths", "vzctl"), self.real_id)
		)

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"vps", "is_running",
			str(self.server['server_id']), str(self.vps_id),
			"0"
		)

		# just report error, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)

		return ovz_exit_codes.translate(exit_code)

	# }}}

	# maintenance {{{

	def passwd(self):
		ret = super(ovz_core, self).passwd()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s set %d --userpasswd \"%s:%s\"" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				executer.escape(self.username),
				executer.escape(self.password)
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def set_hostname(self):
		ret = super(ovz_core, self).set_hostname()
		if ret: return ret

		# continue setting stuff
		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --hostname %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				executer.escape(self.new_hostname),
			)
		)

		return ovz_exit_codes.translate(exit_code)

	# }}}

	# networking {{{

	def add_ip(self):
		ret = super(ovz_core, self).add_ip()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --ipadd %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				executer.escape(self.ip)
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def remove_all_ips(self):
		ret = super(ovz_core, self).remove_all_ips()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --ipdel all" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def remove_ip(self):
		ret = super(ovz_core, self).remove_ip()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --ipdel %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				executer.escape(self.ip)
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def set_dns(self):
		ret = super(ovz_core, self).set_dns()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s set %d --save --nameserver %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				self.dns.replace(" ", " --nameserver ")
			)
		)

		return ovz_exit_codes.translate(exit_code)

	# }}}

	# status {{{

	def load_averages(self):
		ret = super(ovz_core, self).load_averages()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s exec %d %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				glob.config.get("paths", "uptime")
			)
		)

		# error with the status command
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# gather load averages
		stdout_arr = stdout[0].split(" ")
		load_average_1 = stdout_arr[-3].strip(",")
		load_average_5 = stdout_arr[-2].strip(",")
		load_average_15 = stdout_arr[-1].strip(",").strip()

		# start PHP exits
		php_exit_first = False

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"load_average_1", load_average_1
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"load_average_5", load_average_5
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"load_average_15", load_average_15
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# return code
		if php_exit_first:
			return php_exit_codes.translate(php_exit_first)
		else:
			return errors.throw(errors.ERR_SUCCESS)

	def status(self):
		ret = super(ovz_core, self).status()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s status %d" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		# error with the status command
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# split up stdout
		stdout_arr = stdout[0].split(" ")

		# deleted
		if stdout_arr[2] == "deleted":
			is_running = "0"
			ret = errors.VPS_STATUS_DELETED

		# running
		elif stdout[0][-9:] == " running\n":
			is_running = "1"
			ret = errors.VPS_STATUS_RUNNING

		# stopped
		else:
			is_running = "0"
			ret = errors.VPS_STATUS_STOPPED

		# update DB
		(php_exit_code,stdout,stderr) = php.db_update(
			"vps", "is_running",
			str(self.server['server_id']), str(self.vps_id),
			is_running
		)

		# just report error, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)

		return ret

	# extra commands to add to the threading
	def status_update_all(self):
		ret = super(ovz_core, self).status_update_all([
			self.usage_beancounters
		])
		return ret

	def uptime(self):
		ret = super(ovz_core, self).uptime()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s exec %d %s" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				glob.config.get("paths", "uptime")
			)
		)

		# error with the status command
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# get uptime
		stdout_arr = stdout[0].split(",")
		uptime = stdout_arr[0][stdout_arr[0].index("up")+2:].strip()
		if uptime[-4:] == "days":
			uptime += " " + stdout_arr[1].strip()

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"uptime", uptime
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	def usage_bandwidth(self):
		ret = super(ovz_core, self).usage_bandwidth()
		if ret: return ret
		return errors.throw(errors.ERR_UNKNOWN)

	def usage_cpu(self):
		ret = super(ovz_core, self).usage_cpu()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s exec %d \"%s\" | " % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				glob.config.get("paths", "vmstat")
			) + \
			"/usr/bin/env sed -r \"s/[ ]+/ /g\""
		)

		# command failed
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# parse out CPU info
		data_line = stdout[2]
		data_arr = data_line.split(" ")
		usage_cpu_user = data_arr[-5]
		usage_cpu_system = data_arr[-4]
		usage_cpu_idle = data_arr[-3]

		# start PHP exits
		php_exit_first = False

		# user CPU usage
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_cpu_user", usage_cpu_user
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# system CPU usage
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_cpu_system", usage_cpu_system
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# idle CPU usage
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_cpu_idle", usage_cpu_idle
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# return code
		if php_exit_first:
			return php_exit_codes.translate(php_exit_first)
		else:
			return errors.throw(errors.ERR_SUCCESS)

	def usage_disk(self):
		ret = super(ovz_core, self).usage_disk()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s exec %d \"/usr/bin/env df -B 1K -P\" | " % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			) + \
			"/usr/bin/env grep -E \" /$\" | " + \
			"/usr/bin/env sed -r \"s/^[^ ]+[ ]+[^ ]+[ ]+([0-9]+) .*$/\\1/g\""
		)

		# command failed
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_disk", stdout[0].strip()
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	def usage_mem(self):
		ret = super(ovz_core, self).usage_mem()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s exec %d \"/usr/bin/env cat /proc/meminfo\"" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		# command failed
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# get string version of stdout
		str_stdout = " ".join(stdout)

		# get total
		match = re.search("MemTotal:[ ]*([0-9]+)", str_stdout)
		total = int(match.groups()[0])

		# get free
		match = re.search("MemFree:[ ]*([0-9]+)", str_stdout)
		free = int(match.groups()[0])

		# get cached
		match = re.search("Cached:[ ]*([0-9]+)", str_stdout)
		cache = int(match.groups()[0])

		# calculate usage
		usage = total - free - cache

		# update db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_mem", str(usage)
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	def users(self):
		ret = super(ovz_core, self).users()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute(
			"%s exec %d \"/usr/bin/env who -q\" | " % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			) + \
			"/usr/bin/env grep -vE \"^#\""
		)

		# command failed
		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# update db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"users", stdout[0].strip()
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	# }}}

	# internal VPS commands {{{

	def service_restart(self):
		ret = super(ovz_core, self).service_restart()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s exec %d %s restart" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				os.path.join(
					glob.config.get("paths", "initd"),
					executer.escape(self.service)
				)
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def service_start(self):
		ret = super(ovz_core, self).service_start()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s exec %d %s start" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				os.path.join(
					glob.config.get("paths", "initd"),
					executer.escape(self.service)
				)
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def service_stop(self):
		ret = super(ovz_core, self).service_stop()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s exec %d %s stop" % (
				glob.config.get("paths", "vzctl"),
				self.real_id,
				os.path.join(
					glob.config.get("paths", "initd"),
					executer.escape(self.service)
				)
			)
		)

		return ovz_exit_codes.translate(exit_code)

	# }}}

	# OVZ specific {{{

	def mount(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		(exit_code,_,_) = self.do_execute(
			"%s mount %d" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def umount(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		(exit_code,_,_) = self.do_execute(
			"%s umount %d" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		return ovz_exit_codes.translate(exit_code)

	def usage_beancounters(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		(exit_code,beancounters,_) = self.do_execute(
			"%s exec %d cat /proc/user_beancounters" % (
				glob.config.get("paths", "vzctl"),
				self.real_id
			)
		)

		if exit_code != 0:
			return ovz_exit_codes.translate(exit_code)

		# update db
		(php_exit_code,tmp,tmp2) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_beancounters", "\n".join(beancounters)
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	# }}}
