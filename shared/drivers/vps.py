#!/usr/bin/env python

# Panenthe: vps.py
# Defines an abstract class that controls VPSes.

import glob
import errors
from attrdict import attrdict
from iptables import iptables
import api
import php
import php_exit_codes
from generic_thread import generic_thread
import server

##

from execute import *

import sys

class vps(attrdict):
	def __init__(self, dict):
		super(vps, self).__init__(dict)

		# verify all fields are present
		if not self.require([
			"vps_id",
			"hostname",
			"driver",
			"server",
			"master"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# force casting of vps fields
		self.force_cast_int("vps_id")

		# verify all master fields are present
		if not self.require_dict(self.master, [
			"server_id",
			"parent_server_id",
			"hostname",
			"ip",
			"port"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# force casting of master fields
		self.force_cast_int_dict("master", [
			"server_id",
			"parent_server_id",
			"port"
		])

		# verify all server fields are present
		if not self.require_dict(self.server, [
			"server_id",
			"parent_server_id",
			"hostname",
			"ip",
			"port"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# force casting of server fields
		self.force_cast_int_dict("server", [
			"server_id",
			"parent_server_id",
			"port"
		])

		# add drivers path to system path for import
		sys.path.append(glob.getfile("drivers", self.driver))

		# start out with undefined executer
		self.executer = None

	# helpers {{{

	def do_execute(self, cmd):
		if self.executer == None:
			return execute_vps(self, cmd)
		else:
			return self.executer.execute(cmd)

	def server_is_slave(self):
		if self.server['parent_server_id'] == 0:
			return False
		else:
			return True

	def get_server(self):
		thedict = dict(self.master)
		thedict['remote_server'] = dict(self.server)
		srv = server.server(thedict)
		return srv

	# }}}

	# meta {{{

	def next_id(self):
		pass

	def get_distro(self, root = "/"):
		try:
			return self.distro

		except AttributeError:
			(exit_code,_,_) = self.do_execute(
				"test -e \"%s\"" % os.path.join(
					root, "etc/redhat-release"
				)
			)

			if exit_code == 0:
				self.distro = "redhat"

			else:
				(exit_code,_,_) = self.do_execute(
					"test -e \"%s\"" % os.path.join(
						root,
						"etc/debian_version"
					)
				)

				if exit_code == 0:
					self.distro = "debian"

				else:
					self.distro = "unknown"

			return self.distro

	# }}}

	# database {{{

	def lock(self):
		# update PHP
		(php_exit_code,_,_) = php.db_update(
			"vps", "is_locked", str(self.vps_id), "1"
		)

		# php exit code
		if php_exit_code != 0:
			return php_exit_codes.translate(php_exit_code)

		return errors.throw(errors.ERR_SUCCESS)

	def unlock(self):
		# update PHP
		(php_exit_code,_,_) = php.db_update(
			"vps", "is_locked", str(self.vps_id), "0"
		)

		# php exit code
		if php_exit_code != 0:
			return php_exit_codes.translate(php_exit_code)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# management {{{

	def create(self):
		if not self.require([
			"real_id",
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw",
			"ost"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int([
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw"
		])

	def destroy(self):
		if not self.require(["real_id", "force"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_bool("force")

	def modify(self):
		if not self.require([
			"old_data",
			"real_id",
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int([
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw"
		])

		if not self.require_dict(self.old_data, [
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int_dict("old_data", [
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw"
		])

	def ost_create(self):
		if not self.require([
			"ost_name",
			"ost_file",
			"ost_driver_id",
			"ost_arch"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def rebuild(self):
		if not self.require([
			"real_id",
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw",
			"ost"
		]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		self.force_cast_int([
			"disk_space",
			"backup_space",
			"swap_space",
			"g_mem",
			"b_mem",
			"cpu_pct",
			"cpu_num",
			"in_bw",
			"out_bw"
		])

	# the reason we limit both incoming and outgoing packets on each table is
	# that the Xen bridge and other networking stuff use methods that for some
	# reason put route packets into strange places within iptables
	def suspend_ip(self):
		if not self.require("ip"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# FORWARD table incoming
		(exit_code,_,_) = iptables.insert_rule(
			"FORWARD", "-d %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# FORWARD table outgoing
		(exit_code,_,_) = iptables.insert_rule(
			"FORWARD", "-s %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# INPUT table incoming
		(exit_code,_,_) = iptables.insert_rule(
			"INPUT", "-d %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# INPUT table outgoing
		(exit_code,_,_) = iptables.insert_rule(
			"INPUT", "-s %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# OUTPUT table incoming
		(exit_code,_,_) = iptables.insert_rule(
			"OUTPUT", "-d %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# OUTPUT table outgoing
		(exit_code,_,_) = iptables.insert_rule(
			"OUTPUT", "-s %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# save rules
		srv = self.get_server()
		error = iptables.save(srv.do_execute, srv.get_remote_distro())

		if error != errors.ERR_SUCCESS:
			return error

	def unsuspend_ip(self):
		if not self.require("ip"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# FORWARD table incoming
		iptables.delete_rule(
			"FORWARD", "-d %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# FORWARD table outgoing
		iptables.delete_rule(
			"FORWARD", "-s %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# INPUT table incoming
		iptables.delete_rule(
			"INPUT", "-d %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# INPUT table outgoing
		iptables.delete_rule(
			"INPUT", "-s %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# OUTPUT table incoming
		iptables.delete_rule(
			"OUTPUT", "-d %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# OUTPUT table outgoing
		iptables.delete_rule(
			"OUTPUT", "-s %s -m comment --comment panenthe_suspend -j %s" % (
				executer.escape(self.ip),
				glob.config.get("server", "suspend_mode")
			),
			self.do_execute
		)

		# save rules
		srv = self.get_server()
		error = iptables.save(srv.do_execute, srv.get_remote_distro())

		if error != errors.ERR_SUCCESS:
			return error

	# }}}

	# maintenance {{{

	def passwd(self):
		if not self.require(["real_id", "username", "password"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def set_hostname(self):
		if not self.require(["real_id", "new_hostname"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	# }}}

	# control {{{

	def poweroff(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def reboot(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def start(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def stop(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	# }}}

	# networking {{{

	def add_ip(self):
		if not self.require(["real_id", "ip", "netmask", "gateway"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# incoming traffic
		(exit_code,_,_) = iptables.add_rule(
			"PANENTHE_BW", "-d %s" % self.ip, self.do_execute
		)

		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# outgoing traffic
		(exit_code,_,_) = iptables.add_rule(
			"PANENTHE_BW", "-s %s" % self.ip, self.do_execute
		)

		if exit_code != 0:
			return errors.throw(errors.SERVER_IPTABLES)

		# save rules
		srv = self.get_server()
		error = iptables.save(srv.do_execute, srv.get_remote_distro())

		if error != errors.ERR_SUCCESS:
			return error

	def remove_all_ips(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# vps IP addresses
		ac = api.api_call("vm_ips", {
			'vps_id': self.vps_id
		})
		ret = ac.execute()
		if ret != errors.ERR_SUCCESS: return ret

		# remove IPs from iptables
		try:
			ips = ac.output()[0]['ip']

			# loop through IPs and remove
			for ip in ips:
				iptables.delete_rule(
					"PANENTHE_BW", "-d %s" % ip, self.do_execute
				)
				iptables.delete_rule(
					"PANENTHE_BW", "-s %s" % ip, self.do_execute
				)

		except (IndexError, KeyError): pass

		# save rules
		srv = self.get_server()
		error = iptables.save(srv.do_execute, srv.get_remote_distro())

		if error != errors.ERR_SUCCESS:
			return error

	def remove_ip(self):
		if not self.require(["real_id", "ip"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# incoming traffic
		iptables.delete_rule(
			"PANENTHE_BW", "-d %s" % self.ip, self.do_execute
		)

		# outgoing traffic
		iptables.delete_rule(
			"PANENTHE_BW", "-s %s" % self.ip, self.do_execute
		)

		# save rules
		srv = self.get_server()
		error = iptables.save(srv.do_execute, srv.get_remote_distro())

		if error != errors.ERR_SUCCESS:
			return error

	def set_dns(self):
		if not self.require(["real_id", "dns"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	# }}}

	# status/statistics {{{

	def status_update_all(self, extra_status = None, replace_status = None):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# if stopped, don't continue
		status = self.status()
		if status != errors.VPS_STATUS_RUNNING:
			return status

		# ssh master start
		self.executer = executer_vps(self)
		self.executer.ssh_master_start()
		self.executer.thread_safe = True

		# replace status completely
		if replace_status != None:
			threads = replace_status

		# setup status threads
		else:
			threads = [
				generic_thread(self.load_averages),
				generic_thread(self.uptime),
				generic_thread(self.usage_bandwidth),
				generic_thread(self.usage_cpu),
				generic_thread(self.usage_disk),
				generic_thread(self.usage_mem),
				generic_thread(self.users)
			]

			# add extra status fields defined by any drivers
			for status in extra_status:
				threads.append(generic_thread(status))

		# execute status threads
		for thread in threads:
			thread.start()

		# wait for threads
		for thread in threads:
			thread.join()

		# get first error
		error_first = None
		for thread in threads:
			if thread.ret != errors.ERR_SUCCESS:
				error_first = thread.ret
				break

		# ssh master stop
		self.executer.ssh_master_stop()
		self.executer = None

		# return error
		if error_first:
			return error_first

		# return status of VPS
		return self.status()

	def load_averages(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def status(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def uptime(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def usage_bandwidth(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# vps IP addresses
		ac = api.api_call("vm_get_info", {
			'vps_id': self.vps_id
		})
		ret = ac.execute()
		if ret != errors.ERR_SUCCESS: return ret
		result = ac.output()

		# check output
		try:
			self.ips = result[0]['ip']

		# error with output
		except IndexError:
			return errors.throw(errors.BACKEND_ERR_UNKNOWN)

		# no IPs associated with it, so nothing to update
		except KeyError:
			return errors.throw(errors.ERR_SUCCESS)

		# get IP stats
		total_destination = 0
		total_source = 0
		for ip in self.ips:
			(exit_code,stdout,_) = self.do_execute(
				"%s -nvxL PANENTHE_BW | /usr/bin/env grep \"%s\" | " % (
					glob.config.get("paths", "iptables"), ip
				) +
				"/usr/bin/env sed -r \"s/[ ]+/ /g\""
			)

			for line in stdout:
				data = line.split(" ")

				# 2 is bytes, 7 is source IP, 8 is destination IP
				if data[7] == "0.0.0.0/0" and data[8] != "0.0.0.0/0":
					total_destination += int(data[2])
				elif data[8] == "0.0.0.0/0" and data[7] != "0.0.0.0/0":
					total_source += int(data[2])

		# update PHP
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_bandwidth",
			str(self.server['server_id']), str(self.vps_id),
			str(total_destination), str(total_source)
		)

		# php exit code
		if php_exit_code != 0:
			return php_exit_codes.translate(php_exit_code)

		# set up iptables for the rules since PHP was updated successfully
		for ip in self.ips:
			# add #1
			(exit_code,_,_) = iptables.add_rule(
				"PANENTHE_BW", "-d %s" % ip, self.do_execute
			)

			if exit_code != 0:
				return errors.throw(errors.SERVER_IPTABLES)

			# add #2
			(exit_code,_,_) = iptables.add_rule(
				"PANENTHE_BW", "-s %s" % ip, self.do_execute
			)

			if exit_code != 0:
				return errors.throw(errors.SERVER_IPTABLES)

		return errors.throw(errors.ERR_SUCCESS)

	def usage_cpu(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def usage_disk(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def usage_mem(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def users(self):
		if not self.require("real_id"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	# }}}

	# internal VPS commands {{{

	def service_restart(self):
		if not self.require(["real_id", "service"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def service_start(self):
		if not self.require(["real_id", "service"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	def service_stop(self):
		if not self.require(["real_id", "service"]):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

	# }}}
