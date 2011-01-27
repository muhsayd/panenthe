#!/usr/bin/env python

# Panenthe: iptables.py
# Defines some interactions with iptables

import glob
import errors

##

import os

class iptables(object):

	@staticmethod
	def ipt_execute(executer, extra_arg, cmd):
		if extra_arg == None:
			(exit_code,stdout,stdin) = executer(cmd)
		else:
			(exit_code,stdout,stdin) = executer(extra_arg, cmd)

		return (exit_code,stdout,stdin)

	@staticmethod
	def save(executer, distro):
		if distro == "redhat":
			(exit_code,_,_) = executer("/sbin/service iptables save")

			if exit_code != 0:
				return errors.throw(errors.SERVER_IPTABLES)

		elif distro == "debian":
			(exit_code,_,_) = executer("/etc/init.d/pan_iptables save")

			if exit_code != 0:
				return errors.throw(errors.SERVER_IPTABLES)

		# unknown distro
		else:
			return errors.throw(errors.SERVER_IPTABLES)

		# no return? must mean success
		return errors.throw(errors.ERR_SUCCESS)

	# chains {{{

	@staticmethod
	def add_chain(chain, executer, extra_arg = None):
		# sanity
		iptables.delete_chain(chain, executer, extra_arg)

		# add chain
		(exit_code,stdout,stderr) = iptables.ipt_execute(executer, extra_arg,
			"%s -N %s" % (glob.config.get("paths", "iptables"), chain)
		)

		return (exit_code,stdout,stderr)

	@staticmethod
	def delete_chain(chain, executer, extra_arg = None):
		# flush the chain
		iptables.ipt_execute(executer, extra_arg, "%s -F %s" % (
			glob.config.get("paths", "iptables"), chain
		))

		# delete the chain itself
		iptables.ipt_execute(executer, extra_arg, "%s -X %s" % (
			glob.config.get("paths", "iptables"), chain
		))

	# }}}

	# rules {{{

	# this rule appends the rule to the end of the chain
	@staticmethod
	def add_rule(chain, rule, executer, extra_arg = None):
		# sanity
		iptables.delete_rule(chain, rule, executer, extra_arg)

		# perform add
		(exit_code,stdout,stderr) = iptables.ipt_execute(executer, extra_arg,
			"%s -A %s %s" % (glob.config.get("paths", "iptables"), chain, rule)
		)

		return (exit_code,stdout,stderr)

	# this rule inserts the rule at the front of the chain
	@staticmethod
	def insert_rule(chain, rule, executer, extra_arg = None):
		# sanity
		iptables.delete_rule(chain, rule, executer, extra_arg)

		# perform insert
		(exit_code,stdout,stderr) = iptables.ipt_execute(executer, extra_arg,
			"%s -I %s 1 %s" % (
				glob.config.get("paths", "iptables"), chain, rule
			)
		)

		return (exit_code,stdout,stderr)

	@staticmethod
	def delete_rule(chain, rule, executer, extra_arg = None):
		# loop and delete all rules (maximum of 20 tries... usually something is
		# up if iptables is running that many times
		exit_code = 0
		i = 0
		while exit_code == 0 and i < 20:
			(exit_code,_,_) = iptables.ipt_execute(executer, extra_arg,
				"%s -D %s %s" % (
					glob.config.get("paths", "iptables"), chain, rule
				)
			)
			i += 1

	# }}}
