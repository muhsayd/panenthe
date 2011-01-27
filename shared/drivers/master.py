#!/usr/bin/env python

# Panenthe: master.py
# Defines an abstract class that controls the master server.

import glob
import errors
from server import server

##

import os

import api
from execute import *

class master(server):
	def __init__(self, dict):
		super(master, self).__init__(dict)

	# overridden methods {{{

	def do_execute(self, cmd):
		return execute(cmd)

	def get_server_id(self):
		return self.server_id

	# }}}

	# cron {{{

	def cron_daily(self):
		# get servers
		ac = api.api_call("server_list", {})
		ret = ac.execute()
		if ret != errors.ERR_SUCCESS: return ret
		servers = ac.output()

		# loop servers
		for remote_server_dict in servers:
			# nothing update on master
			if remote_server_dict['parent_server_id'] == 0:
				continue

			# master
			server_dict = self.get_dict()
			# slave
			server_dict['remote_server'] = remote_server_dict

			# call its cron_daily command
			server_obj = server(server_dict)
			ret = server_obj.cron_daily()
			if ret != errors.ERR_SUCCESS:
				return ret

		# NO RETURN UNLESS FAILED, this is a cron!

	def initialize_bw_cron(self):
		# delete cron (sanity)
		self.uinitialize_bw_cron()

		# add cronjob
		self.do_execute(
			"echo \"\n# panenthe_bw\n17 3 * * * root " +
			glob.getfile("shared/drivers/backend.py") + " " +
			"masterctl cron_daily -d \\\"%s\\\" # panenthe_bw\" >> %s" % (
				str(self.get_dict()),
				glob.config.get("paths", "crontab")
			)
		)

		return errors.throw(errors.ERR_SUCCESS)

	def uinitialize_bw_cron(self):
		# delete cron
		self.do_execute("sed -r \"/# panenthe_bw$/d\" -i \"%s\"" %
			glob.config.get("paths", "crontab")
		)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# network stuff {{{

	def set_hostname(self):
		if not self.require("new_hostname"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)
		# CentOS specific

		# change kernel hostname
		execute("hostname \"%s\"" % executer.escape(self.new_hostname))

		# filter any existing copies of the new hostname in /etc/hosts
		execute("sed \"/[ \t]%s$/d\" -i /etc/hosts" %
			executer.escape(self.new_hostname)
		)

		# filter any existing copies of the current hostname in /etc/hosts
		execute("sed \"/[ \t]%s$/d\" -i /etc/hosts" %
			executer.escape(self.hostname)
		)

		# add hostname to /etc/hosts
		execute("echo \"127.0.0.1 %s\" >> /etc/hosts" %
			executer.escape(self.new_hostname)
		)

		# change /etc/sysconfig/network file
		execute("sed \"s/^HOSTNAME=.*$/HOSTNAME=\\\"%s\\\"/g\" " %
			executer.escape(self.new_hostname) +
			"-i /etc/sysconfig/network"
		)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# public keys {{{

	# rewrite the SSH config file without our stuff in it
	def die_reconfig(self, ssh_config):
		file = open(glob.config.get("paths", "user_ssh_config"), "w")
		file.write(ssh_config)
		file.close()

	def generate_keys(self):
		# LOL WTF AM I DOING.  THAT -p SWITCH IS COMPLETELY USELESS HERE
		(exit_code,_,_) = execute("mkdir -p %s" %
			glob.config.get("paths", "user_ssh_config_dir")
		)
		if exit_code != 0:
			return errors.throw(errors.SERVER_MKDIR_ERROR)

		# remove any existing Panenthe keys
		try:
			os.unlink(glob.config.get("paths", "master_private_key"))
		except OSError: pass
		try:
			os.unlink(glob.config.get("paths", "master_public_key"))
		except OSError: pass

		# generate the trusted SSH key
		(exit_code,_,_) = execute(
			"ssh-keygen -f %s -P \"\"" %
				glob.config.get("paths", "master_private_key")
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_SSH_KEYGEN)

		# get trusted SSH key
		file = open(glob.config.get("paths", "master_public_key"), "r")
		public_key = file.read()
		file.close()

		# overwrite SSH key with identifier we can use to delete the key with
		file = open(glob.config.get("paths", "master_public_key"), "w")
		file.write(public_key[:-1] + "_panenthe\n")
		file.close()

		return errors.throw(errors.ERR_SUCCESS)

	def install_key(self):
		ret = self.require_remote()
		if ret: return ret

		# also require password
		if not self.require_dict(self.remote_server, "password"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# open config file and get the current contents
		try:
			file = open(glob.config.get("paths", "user_ssh_config"), "r")
			ssh_config = file.read()
			file.close()
		# no such file so just create it blank later
		except IOError:
			ssh_config = ""

		# define the server since ssh-copy-id doesn't have a port switch
		file = open(glob.config.get("paths", "user_ssh_config"), "a")
		file.write("\n" +
			"Host panenthe_" + self.remote_server['ip'] + "\n" +
			"Hostname " + self.remote_server['ip'] + "\n" +
			"User root\n" +
			"Port " + str(self.remote_server['port']) + "\n"
		)
		file.close()

		execute(
			"sed -r \"/_" + self.remote_server['ip'] + "$/d\" " +
			"-i %s" % glob.config.get("paths", "user_ssh_known_hosts")
		)

		# sanity check
		# grab the key for the remote host
		(exit_code,_,_) = execute(
			"ssh-keyscan -t rsa,dsa -p " + str(self.remote_server['port']) +
			" " + self.remote_server['ip'] + " >> %s" %
				glob.config.get("paths", "user_ssh_known_hosts")
		)
		if exit_code != 0:
			self.die_reconfig(ssh_config)
			return errors.throw(errors.SERVER_REMOTE_KEYSCAN)

		# send the key to the server
		(exit_code,stdout,_) = execute("expect -c \"log_user 0\n" +
			"spawn ssh-copy-id -i %s panenthe_" %
				glob.config.get("paths", "master_public_key") +
				self.remote_server['ip'] + "\n" +
			"expect {\n" +
			"	\\\"password: \\\" {\n" +
			"		send \\\"" + self.remote_server['password'] + "\\r\\\"\n" +
			"		expect {\n" +
			"			\\\"Permission denied\\\" {\n" +
			"				expect \\\"password: \\\"\n" +
			"				send \\\"\\r\\\"\n" +
			"				expect \\\"password: \\\"\n" +
			"				send \\\"\\r\\\"\n" +
			"				expect eof\n" +
			"				send_user \\\"INVALID_PASSWORD\\\"\n" +
			"				exit\n" +
			"			}\n" +
			"			eof {\n" +
			"				send_user SUCCESS\n" +
			"				exit\n" +
			"			}\n" +
			"		}\n" +
			"	}\n" +
			"\n" +
			"	ERROR {\n" +
			"		send_user PRIVATE_KEY_NOT_FOUND\n" +
			"		exit\n" +
			"	}\n" +
			"\n" +
			"	\\\"No route to host\\\" {\n" +
			"		send_user NO_ROUTE_TO_HOST\n" +
			"		exit\n" +
			"	}\n" +
			"\n" +
			"	\\\"Could not resolve \\\" {\n" +
			"		send_user COULD_NOT_RESOLVE\n" +
			"		exit\n" +
			"	}\n" +
			"\n" +
			"	eof {\n" +
			"		send_user KEY_ALREADY_INSTALLED\n" +
			"		exit\n" +
			"	}\n" +
			"}\n" +
		"\"")

		# reconfigure user_ssh_config
		self.die_reconfig(ssh_config)

		# expect's error code
		if exit_code != 0:
			return errors.throw(errors.SERVER_REMOTE_KEYCOPY)

		# invalid password
		if stdout[0] == "INVALID_PASSWORD":
			return errors.throw(errors.SERVER_INVALID_PASSWORD)

		# no key found
		elif stdout[0] == "PRIVATE_KEY_NOT_FOUND":
			return errors.throw(errors.SERVER_PRIVATE_KEY_NOT_FOUND)

		# no route to host
		elif stdout[0] == "NO_ROUTE_TO_HOST":
			return errors.throw(errors.SERVER_NO_ROUTE_TO_HOST)

		# could not resolve
		elif stdout[0] == "COULD_NOT_RESOLVE":
			return errors.throw(errors.SERVER_COULD_NOT_RESOLVE)

		# key already installed
		elif stdout[0] == "KEY_ALREADY_INSTALLED":
			return errors.throw(errors.SERVER_KEY_ALREADY_INSTALLED)

		# success!
		elif stdout[0] == "SUCCESS":
			return errors.throw(errors.ERR_SUCCESS)

		# unknown error
		return errors.throw(errors.SERVER_ERR_UNKNOWN)

	def remove_local_keys(self):
		# remove private key
		try:
			os.stat(glob.config.get("paths", "master_private_key"))
		except OSError: pass

		# remote public key
		try:
			os.stat(glob.config.get("paths", "master_public_key"))
		except OSError: pass

		return errors.throw(errors.ERR_SUCCESS)

	def remove_key(self):
		ret = self.require_remote()
		if ret: return ret

		# strip keys from remote server
		(exit_code,_,_) = execute(
			"ssh -i %s " % glob.config.get("paths", "master_private_key") +
			"-p " + str(self.remote_server['port']) + " root@" +
			self.remote_server['ip'] + " " +
			"\"sed -r \\\"/^.*_panenthe$/d\\\" -i %s\"" %
				glob.config.get("paths", "user_ssh_authorized_keys")
		)

		# fail
		if exit_code != 0:
			return errors.throw(errors.SERVER_REMOTE_REMOVE)

		# return
		return errors.throw(errors.ERR_SUCCESS)

	# }}}
