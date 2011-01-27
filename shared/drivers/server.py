#!/usr/bin/env python

# Panenthe: server.py
# Defines an abstract class that controls physical servers.

import glob
import errors
from attrdict import attrdict

import api
from execute import *
from iptables import iptables
import php
import php_exit_codes

##

import os
import re

class server(attrdict):
	def __init__(self, dict):
		super(server, self).__init__(dict)

		# NOTE: if you change the server requirements here, make sure to change
		# them in driver.py as well

		# verify all fields are present
		if not self.require([
			"server_id",
			"parent_server_id",
			"hostname",
			"ip",
			"port"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# force casting of vps fields
		self.force_cast_int([
			"server_id",
			"parent_server_id",
			"port"
		])

		# no executer until a function makes one
		self.executer = None

	# helpers {{{

	def do_execute(self, cmd):
		ret = self.require_remote()
		if ret: return (None,None,None)

		if self.executer:
			return self.executer.execute(cmd)
		else:
			return execute_srv(self.remote_server, cmd)

	# execute a series of commands using master/slave connections
	def do_initialize_slavery(self):
		ret = self.require_remote()
		if ret: return (None,None,None)

		if not self.executer:
			self.executer = executer_srv(self.remote_server)
			self.executer.ssh_master_start()

	# restore civil rights
	def do_restore_civil_rights(self):
		if self.executer:
			self.executer.ssh_master_stop()

	def get_server_id(self):
		ret = self.require_remote()
		if ret: return (None,None,None)

		return self.remote_server['server_id']

	# when serverctl is called, the immediate data in self is the master info...	# this requires the remote server information
	def require_remote(self):
		if not self.require_dict(self.remote_server, [
			"server_id",
			"parent_server_id",
			"hostname",
			"ip",
			"port"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int_dict("remote_server", [
			"server_id",
			"parent_server_id",
			"port"
		])

	def get_remote_distro(self):
		try:
			return self.remote_server['distro']

		except KeyError:
			(exit_code,_,_) = self.do_execute("test -e /etc/redhat-release")

			if exit_code == 0:
				self.remote_server['distro'] = "redhat"

			else:
				(exit_code,_,_) = self.do_execute("test -e /etc/debian_version")

				if exit_code == 0:
					self.remote_server['distro'] = "debian"

				else:
					self.remote_server['distro'] = "unknown"

			return self.remote_server['distro']

	def get_remote_cpu_bits(self):
		try:
			return self.remote_server['bits']

		except KeyError:
			(exit_code,stdout,_) = self.do_execute("uname -i")

			if exit_code == 0:
				self.remote_server['bits'] = "32" # just to be safe

			else:
				if len(stdout)>0 and stdout[0] == "x86_64":
					self.remote_server['bits'] = "64"

				else:
					self.remote_server['bits'] = "32"

			return self.remote_server['bits']

	# synchronizes a file on the master to a file on a slave, mostly used for OS
	# templates
	def file_sync(self, sync_file):
		# see if file exists
		(exit_code,_,_) = self.do_execute("test -e %s" % sync_file)

		# copy flag
		copy = True

		# OST exists, check MD5
		if exit_code == 0:
			# local md5
			(exit_code,stdout,_) = execute("md5sum %s" % sync_file)

			# no file found locally
			if stdout == []:
				return errors.throw(errors.SERVER_FILE_NOT_FOUND)

			local_md5 = stdout[0].strip().split(" ")[0]

			if exit_code == 0:
				# remote MD5
				(exit_code,stdout,_) = self.do_execute(
					"md5sum %s" % sync_file
				)

				if exit_code == 0:
					try:
						remote_md5 = stdout[0].strip().split(" ")[0]
						remote_success = True

					except IndexError:
						remote_success = False

				else:
					remote_success = False

				# compare
				if remote_success and local_md5 == remote_md5:
					copy = False

				# remove existing template
				else:
					self.do_execute("rm -f %s" % sync_file)

		# copy over OST
		if copy:
			self.do_execute("mkdir -p \"%s\"" % os.path.dirname(sync_file))

			execute(
				"scp -i %s -P %d %s root@%s:%s" % (
					glob.config.get("paths", "master_private_key"),
					self.remote_server['port'],
					sync_file,
					executer.escape(self.remote_server['ip']), sync_file
				)
			)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# database {{{

	def lock(self):
		# update PHP
		(php_exit_code,_,_) = php.db_update(
			"server", "is_locked", str(self.server_id), "1"
		)

		# php exit code
		if php_exit_code != 0:
			return php_exit_codes.translate(php_exit_code)

		return errors.throw(errors.ERR_SUCCESS)

	def unlock(self):
		# update PHP
		(php_exit_code,_,_) = php.db_update(
			"server", "is_locked", str(self.server_id), "0"
		)

		# php exit code
		if php_exit_code != 0:
			return php_exit_codes.translate(php_exit_code)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# control {{{

	def reboot(self):
		(exit_code,_,_) = self.do_execute(
			"%s -r now" % glob.config.get("paths", "shutdown")
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_SHUTDOWN)

		return errors.throw(errors.ERR_SUCCESS)

	def shutdown(self):
		(exit_code,_,_) = self.do_execute(
			"%s -h now" % glob.config.get("paths", "shutdown")
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_SHUTDOWN)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# setup {{{

	def cleanup_bw(self):
		self.require_remote()

		# delete rules
		iptables.delete_rule("FORWARD", "-j PANENTHE_BW", self.do_execute)

		# delete chains
		iptables.delete_chain("PANENTHE_BW", self.do_execute)

		# delete cron
		self.do_execute("sed -r \"/# panenthe_bw$/d\" -i \"%s\"" %
			glob.config.get("paths", "crontab")
		)

		# save iptables rules
		error = iptables.save(self.do_execute, self.get_remote_distro())

		return error

	def initialize_bw(self):
		self.require_remote()

		# sanity (of which I have none)
		self.cleanup_bw()

		# create chains
		(exit_code,_,_) = iptables.add_chain("PANENTHE_BW", self.do_execute)

		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# create rule for INPUT table
		(exit_code,_,_) = iptables.insert_rule(
			"INPUT", "-j PANENTHE_BW", self.do_execute
		)

		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# create rule for FORWARD table
		(exit_code,_,_) = iptables.insert_rule(
			"FORWARD", "-j PANENTHE_BW", self.do_execute
		)

		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# create rule for OUTPUT table
		(exit_code,_,_) = iptables.insert_rule(
			"OUTPUT", "-j PANENTHE_BW", self.do_execute
		)

		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# server IP addresses
		ac = api.api_call("server_ips", {
			'server_id': self.get_server_id()
		})
		ret = ac.execute()
		if ret != errors.ERR_SUCCESS: return ret
		result = ac.output()

		# use IPs
		try:
			result[0]
			result[0]['ip']
			ips = result[0]['ip']

			# loop through IPs
			for ip in ips:
				iptables.add_rule("PANENTHE_BW", "-d %s" % ip, self.do_execute)
				iptables.add_rule("PANENTHE_BW", "-s %s" % ip, self.do_execute)

		# there might not be any IPs yet
		except (IndexError, KeyError): pass

		# save iptables rules
		error = iptables.save(self.do_execute, self.get_remote_distro())

		return error

	def setup(self):
		return self.initialize_bw()

	def usetup(self):
		return self.cleanup_bw()

	# }}}

	# maintenance {{{

	def cron_daily(self):
		self.require_remote()

		# get VMs
		ac = api.api_call("server_vms", {
			'server_id': self.get_server_id()
		})
		ret = ac.execute()
		if ret != errors.ERR_SUCCESS: return ret
		result = ac.output()

		# get backend
		from backend import backend

		# update stats on each VPS
		for vps in result:
			vps['server'] = self.remote_server
			backend.execute("vpsctl", "usage_bandwidth", str(vps))

		# we return here because this is never called by the cron directly...
		# the cron is actually called by cron_daily() of masterctl
		return errors.throw(errors.ERR_SUCCESS)

	def passwd(self):
		if not self.require([
			"username",
			"password"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# password change command
		(exit_code,_,_) = self.do_execute(
			"echo \"%s\" | " % executer.escape(self.password) +
			"/usr/bin/env passwd --stdin \"%s\"" %
				executer.escape(self.username)
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_PASSWD)

		return errors.throw(errors.ERR_SUCCESS)

	def ssh_port(self):
		if not self.require([
			"new_ssh_port"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int("new_ssh_port")

		# change SSH port command
		self.do_execute(
			"sed -r \"s/^[ ]*[#]?[ ]*Port[ ]+[0-9]*[ ]*$/Port %d/g\" -i %s" % (
				self.new_ssh_port,
				glob.config.get("paths", "sshd_config")
			)
		)

		# restart SSH command
		(exit_code,_,_) = self.do_execute(
			glob.config.get("paths", "initd") + "/sshd restart"
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_SSH_PORTCHANGE)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# stats {{{

	def status_update_all(self):
		error_first = False

		# processor info
		error = self.stat_procs()
		if not error_first:
			error_first = error

		# load info
		error = self.stat_load()
		if not error_first:
			error_first = error

		# uptime info
		error = self.stat_uptime()
		if not error_first:
			error_first = error

		# memory info
		error = self.stat_mem()
		if not error_first:
			error_first = error

		# disk info
		error = self.stat_disk()
		if not error_first:
			error_first = error

		# kernel images info
		error = self.stat_images()
		if not error_first:
			error_first = error

		# return error
		if not error_first:
			return errors.throw(errors.ERR_SUCCESS)
		else:
			return error_first

	def stat_procs(self):
		# get cpuinfo
		(exit_code,cpuinfo,_) = self.do_execute(
			"/usr/bin/env cat /proc/cpuinfo"
		)

		# cpuinfo error
		if exit_code != 0:
			return errors.throw(errors.SERVER_CPUINFO)

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"cpuinfo", "\n".join(cpuinfo)
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	def stat_load(self):
		# execute uptime
		(exit_code,stdout,_) = self.do_execute(
			glob.config.get("paths", "uptime")
		)

		# error with the uptime command
		if exit_code != 0:
			return errors.throw(errors.SERVER_UPTIME)

		# gather load averages
		stdout_arr = stdout[0].split(" ")
		load_average_1 = stdout_arr[-3].strip(",")
		load_average_5 = stdout_arr[-2].strip(",")
		load_average_15 = stdout_arr[-1].strip(",")

		# start PHP exits
		php_exit_first = False

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"load_average_1", load_average_1
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"load_average_5", load_average_5
		)

		# just throw, DO NOT RETURN
		if php_exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"load_average_15", load_average_15
		)

		# return code
		if php_exit_first:
			return php_exit_codes.translate(php_exit_first)
		else:
			return errors.throw(errors.ERR_SUCCESS)

	def stat_uptime(self):
		# execute uptime
		(exit_code,stdout,_) = self.do_execute(
			glob.config.get("paths", "uptime")
		)

		# error with the uptime command
		if exit_code != 0:
			return errors.throw(errors.SERVER_UPTIME)

		# get uptime
		stdout_arr = stdout[0].split(",")
		uptime = stdout_arr[0][stdout_arr[0].index("up")+2:].strip()
		if uptime[-4:] == "days":
			uptime += " " + stdout_arr[1].strip()

		# update stats db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"uptime", uptime
		)

		# return code
		return php_exit_codes.translate(php_exit_code)

	def stat_mem(self):
		# get meminfo
		(exit_code,stdout,_) = self.do_execute(
			"/usr/bin/env cat /proc/meminfo"
		)

		# meminfo error
		if exit_code != 0:
			return errors.throw(errors.SERVER_MEMINFO)

		# get string version of stdout
		str_stdout = " ".join(stdout)

		# get total
		match = re.search("MemTotal:[ ]*([0-9]+)", str_stdout)
		total = int(match.groups()[0])

		# get free
		match = re.search("MemFree:[ ]*([0-9]+)", str_stdout)
		free = int(match.groups()[0])

		# get cached memory
		match = re.search("Cached:[ ]*([0-9]+)", str_stdout)
		cache = int(match.groups()[0])

		# calculate usage
		usage = total - free - cache

		# start PHP exits
		php_exit_first = False

		# update db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"usage_mem", str(usage)
		)

		# just throw, DO NOT RETURN
		if exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# update db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"max_mem", str(total)
		)

		# just throw, DO NOT RETURN
		if exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# return code
		if php_exit_first:
			return php_exit_codes.translate(php_exit_first)
		else:
			return errors.throw(errors.ERR_SUCCESS)

	def stat_disk(self):

		# get disk name
		(exit_code,disk_name,_) = self.do_execute(
			"/usr/bin/env df -B 1K -P | " +
			"/usr/bin/env grep -E \" /$\" | " +
			"/usr/bin/env cut -d ' ' -f1 | " +
			"/usr/bin/env sed -r \"s/[0-9]+$//g\""
		)

		# just the first line
		disk_name = disk_name[0].strip()

		# disk usage error
		if exit_code != 0:
			return errors.throw(errors.SERVER_DISKUSAGE)

		# get disk usage
		(exit_code,disk_info,_) = self.do_execute(
			"/usr/bin/env df -B 1K -P | " +
			"/usr/bin/env grep \"%s\" | " % disk_name +
			"/usr/bin/env sed -r \"s/[ ]+/ /g\""
		)

		# disk usage error
		if exit_code != 0:
			return errors.throw(errors.SERVER_DISKUSAGE)
	
		# get usage and total
		usage = 0
		total = 0
		for info in disk_info:
			disk_info_arr = info.split(" ")
			usage += int(disk_info_arr[2])
			total += int(disk_info_arr[3])

		# start PHP exits
		php_exit_first = False

		# update db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"usage_disk", str(usage)
		)

		# just throw, DO NOT RETURN
		if exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# update db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"max_disk", str(total)
		)

		# just throw, DO NOT RETURN
		if exit_code != 0:
			php_exit_codes.translate(php_exit_code)
			if not php_exit_first:
				php_exit_first = php_exit_code

		# return code
		if php_exit_first:
			return php_exit_codes.translate(php_exit_first)
		else:
			return errors.throw(errors.ERR_SUCCESS)

	def stat_images(self):
		# get image listing
		(exit_code,images,_) = self.do_execute("ls -1 /boot")
		if exit_code != 0:
			return errors.throw(errors.SERVER_LIST_IMAGES)

		new_images = []
		for i in xrange(0, len(images)):
			# only get vmlinu.* stuff
			if not re.match("vmlinu", images[i]):
				continue

			# put xenU kernel to be first kernel
			elif re.search("xenU", images[i]):
				new_images.insert(0, images[i])

			# otherwise tack it onto the end
			else:
				new_images.append(images[i])

		# newline delimited
		nl_images = "\n".join(new_images)

		# update db
		(php_exit_code,_,_) = php.db_update(
			"server_stats", "update_attribute",
			str(self.get_server_id()),
			"kernel_images", nl_images
		)

	# }}}
