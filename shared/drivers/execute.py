#!/usr/bin/env python

# Panenthe: execute.py
# Wrapper to execute external commands.  ALWAYS USE THIS!

import glob

##

import os
import re
import signal
import subprocess
import tempfile
import time

# genericized commands {{{

# execute locally (this should always mean on master)
def execute(cmd):
	e = executer()
	return e.execute(cmd)

# execute using driver information (this should execute on the server)
def execute_drv(drv, cmd):
	e = executer_drv(drv)
	return e.execute(cmd)

# execute using server information (this should execute on the server)
def execute_srv(srv, cmd):
	e = executer_srv(srv)
	return e.execute(cmd)

# executing using driver information (this should execute on the server the VPS
# is hosted on, then various commands should be used to execute commands on the
# VPS itself)
def execute_vps(vps, cmd):
	e = executer_vps(vps)
	return e.execute(cmd)

# }}}

# standard execution {{{

class executer(object):

	def __init__(self):
		self.extra_switches = {}
		self.ssh_master_process = None
		self.thread_safe = False

	def get_extra_switches(self):
		if self.extra_switches == {}:
			return ""

		switches = " "
		for i in self.extra_switches:
			switches += self.extra_switches[i]

		return switches

	# SSH master/slave handling {{{

	# this is for performance... use master/slave ssh connections to have ssh
	# connections piggy back onto each other

	def ssh_master_start(self):
		tmp_socket = tempfile.mkstemp(prefix = "panenthe_ssh")
		ssh_wrappers = self.get_wrap_command()
		os.unlink(tmp_socket[1]) # remove it since ssh will complain

		self.ssh_master_process = os.spawnvpe(os.P_NOWAIT,
			"ssh", (ssh_wrappers[0] +
				" -k -n -x -M -S %s" % tmp_socket[1]
				#"-q"
				#"-C"
			).split(" "),
			os.environ
		)

		if self.ssh_master_process:
			self.extra_switches['master'] = "-S %s" % tmp_socket[1]

	def ssh_master_stop(self):
		self.extra_switches.pop('master')

		if self.ssh_master_process:
			os.kill(self.ssh_master_process, signal.SIGTERM)

	# }}}

	@staticmethod
	def escape(cmd, first = False):
		# slashes
		cmd = re.sub("([\\\\]+?)", "\\\\" + "\\1", cmd)
		# quotes
		cmd = cmd.replace("\"", "\\\"")
		# backticks
		cmd = cmd.replace("`", "\\`")
		# done
		return cmd

	def execute(self, cmd):
		# get command wrap_cmd
		wrap_cmd = self.get_wrap_command()

		# if not standard wrap_cmd, escape command
		e = executer()
		if wrap_cmd != e.get_wrap_command():
			cmd = executer.escape(cmd)

		# execute logging (start)
		self.execute_log_start(cmd, wrap_cmd)

		# execute command
		(exit_code, stdout, stderr, start_time, end_time) = \
			self.execute_start(wrap_cmd, cmd)

		# post operations on the output
		(exit_code,stdout,stderr) = self.post_op(exit_code, stdout, stderr)

		# get log wrap_cmd
		wrap_output = self.get_wrap_log(
			exit_code, cmd, start_time, end_time
		)

		# execute logging (end)
		self.execute_log_end(
			cmd, wrap_cmd, wrap_output, exit_code, stdout, stderr
		)

		return (exit_code,stdout,stderr)

	def execute_start(self, wrap_cmd, cmd):
		# subprocess isn't thread safe
		if not self.thread_safe:

			# get start time
			start_time = time.strftime(glob.config.get("errors", "date_format"))

			# execute command
			proc = subprocess.Popen(
				"%s%s%s" % (wrap_cmd[1], cmd, wrap_cmd[2]),
				shell = True, stdout = subprocess.PIPE, stderr = subprocess.PIPE
			)

			# wait until it's done
			done = False
			while not done:
				try:
					proc.wait()
					done = True
				except OSError: # sometimes the processor is too fast :/
					time.sleep(0.01)
			proc.wait()

			# update codes
			proc.poll()

			# get return data
			stdout = proc.stdout.readlines()
			proc.stdout.close()
			stderr = proc.stderr.readlines()
			proc.stderr.close()
			exit_code = proc.returncode

			# get end time
			end_time = time.strftime("%Y-%m-%d %H:%M:%S")

			return (exit_code, stdout, stderr, start_time, end_time)

		else:
			# create temp files
			tmp_stdout = tempfile.mkstemp(prefix = "panenthe")
			tmp_stderr = tempfile.mkstemp(prefix = "panenthe")

			# get start time
			start_time = time.strftime(glob.config.get("errors", "date_format"))

			# execute command
			exit_code = os.system(
				"(%s%s%s) 1>> %s 2>> %s" % (
					wrap_cmd[1], cmd, wrap_cmd[2],
					tmp_stdout[1],
					tmp_stderr[1]
				)
			)

			# os.system() multiplies the error code by 256
			exit_code /= 256

			# debug
			"""print "(%s%s%s) 1>> %s 2>> %s" % (
				wrap_cmd[1], cmd, wrap_cmd[2],
				tmp_stdout[1],
				tmp_stderr[1]
			)"""

			# get end time
			end_time = time.strftime("%Y-%m-%d %H:%M:%S")

			# get stdout and delete temp file
			fp_tmp_stdout = os.fdopen(tmp_stdout[0])
			stdout = fp_tmp_stdout.readlines()
			fp_tmp_stdout.close()
			os.unlink(tmp_stdout[1])

			# get stderr and delete temp file
			fp_tmp_stderr = os.fdopen(tmp_stderr[0])
			stderr = fp_tmp_stderr.readlines()
			fp_tmp_stderr.close()
			os.unlink(tmp_stderr[1])

			return (exit_code, stdout, stderr, start_time, end_time)

	def execute_log_start(self, cmd, wrap_cmd):
		# store ssh command
		fp_ssh_raw = open(
			glob.getfile(glob.config.get("logging", "ssh_raw")), "a"
		)
		fp_ssh_raw.write("\n\n\n\n%s%s%s" % (wrap_cmd[1], cmd, wrap_cmd[2]))
		fp_ssh_raw.close()

	def execute_log_end(self,
		cmd, wrap_cmd, wrap_output, exit_code, stdout, stderr
	):
		# store stdout
		out_stdout=stdout[:]
		out_stdout.insert(0, wrap_output[0])
		out_stdout.append(wrap_output[1])
		fp_action = open(
			glob.getfile(glob.config.get("logging", "action")), "a"
		)
		fp_action.write("\n".join(out_stdout)+"\n")
		fp_action.close()

		# store stderr, if necessary
		if exit_code != 0 or stderr != []:
			out_stderr=stderr[:]
			out_stderr.insert(0, wrap_output[0])
			out_stderr.append(wrap_output[1])
			fp_stderr = open(
				glob.getfile(glob.config.get("errors", "file_error")), "a"
			)
			fp_stderr.write("\n".join(out_stderr)+"\n")
			fp_stderr.close()

	# no post_op
	def post_op(self, exit_code, stdout, stderr):
		return (exit_code,stdout,stderr)

	# format:
	# [wrap_ssh_master, wrap_front, wrap_back]
	#
	# wrap_ssh_master: command to execute master command
	# wrap_front: command to tack onto the front of the executing command
	# wrap_back: command to tack onto the back of the executing command
	#
	# default: no master command, no command wrapping
	def get_wrap_command(self):
		return [None,"",""]

	# standard log wrapping
	def get_wrap_log(self, exit_code, cmd, start_time, end_time):
		return (
			"%s:PY:STARTCMD: %s" % (start_time, cmd),
			"%s:PY:ENDCMD:%d: %s" % (
				end_time, exit_code, cmd
			)
		)

# }}}

# driver execution {{{

class executer_drv(executer):

	def __init__(self, drv):
		super(executer_drv, self).__init__()
		self.drv = drv

		# require these
		self.ip = self.drv.server['remote_server']['ip']
		self.port = int(self.drv.server['remote_server']['port'])

	def post_op(self, exit_code, stdout, stderr):
		return (exit_code,stdout,stderr)

	def get_wrap_command(self):
		return [
			"ssh -i %s -T -p %d root@%s" % (
				glob.config.get("paths", "master_private_key"),
				self.port,
				executer.escape(self.ip)
			),
			"ssh -i %s%s -T -p %d root@%s \"" % (
				glob.config.get("paths", "master_private_key"),
				self.get_extra_switches(),
				self.port,
				executer.escape(self.ip)
			), "\""
		]

	def get_wrap_log(self, exit_code, cmd, start_time, end_time):
		return [
			"%s:DRV:%d:STARTCMD: %s" % (
				start_time, self.drv.server['server_id'], cmd
			),
			"%s:DRV:%d:ENDCMD:%d: %s" % (
				end_time, self.drv.server['server_id'], exit_code, cmd
			)
		]

# }}}

# server execution {{{

class executer_srv(executer):

	def __init__(self, srv):
		super(executer_srv, self).__init__()
		self.srv = srv

		# require these
		self.srv['ip']
		self.srv['port'] = int(self.srv['port'])

	# SSH exit code translation... SSH returns the exit code of the remote
	# command multiplied by 256
	def post_op(self, exit_code, stdout, stderr):
		exit_code /= 256
		return (exit_code,stdout,stderr)

	def get_wrap_command(self):
		return [
			"ssh -i %s -T -p %d root@%s" % (
				glob.config.get("paths", "master_private_key"),
				self.srv['port'],
				executer.escape(self.srv['ip'])
			),
			"ssh -i %s%s -T -p %d root@%s \"" % (
				glob.config.get("paths", "master_private_key"),
				self.get_extra_switches(),
				self.srv['port'],
				executer.escape(self.srv['ip'])
			), "\""
		]

	def get_wrap_log(self, exit_code, cmd, start_time, end_time):
		return [
			"%s:SRV:%d:STARTCMD: %s" % (start_time, self.srv['server_id'], cmd),
			"%s:SRV:%d:ENDCMD:%d: %s" % (
				end_time, self.srv['server_id'], exit_code, cmd
			)
		]

# }}}

# VPS execution {{{

class executer_vps(executer):

	def __init__(self, vps):
		super(executer_vps, self).__init__()
		self.vps = vps

	def post_op(self, exit_code, stdout, stderr):
		return (exit_code,stdout,stderr)

	def get_wrap_command(self):
		# has a vps defined
		if self.vps!=None and self.vps.server_is_slave():
			return [
				"ssh -i %s -T -p %d root@%s" % (
					glob.config.get("paths", "master_private_key"),
					self.vps.server['port'],
					executer.escape(self.vps.server['ip'])
				),
				"ssh -i %s%s -T -p %d root@%s \"" % (
					glob.config.get("paths", "master_private_key"),
					self.get_extra_switches(),
					self.vps.server['port'],
					executer.escape(self.vps.server['ip'])
				), "\""
			]

		# super instead
		else:
			return super(executer_vps, self).get_wrap_command()

	def get_wrap_log(self, exit_code, cmd, start_time, end_time):
		# has a vps defined
		if self.vps!=None:
			exec("import %s_exit_codes" % self.vps.driver)
			exit_error = eval(
				"%s_exit_codes.translate(exit_code)" % self.vps.driver
			)

			# has a vps
			try:
				self.vps.real_id
				return (
					"%s:VPS:%d:STARTCMD: %s" % (
						start_time, self.vps.real_id, cmd
					),
					"%s:VPS:%d:ENDCMD:%d:%s:%s: %s" % (
						end_time, self.vps.real_id,
						exit_code, exit_error[0], exit_error[1], cmd
					)
				)

			# no VPS
			except AttributeError:
				return (
					"%s:SRV_VPS:STARTCMD: %s" % (start_time, cmd),
					"%s:SRV_VPS:ENDCMD:%d:%s:%s: %s" % (
						end_time, exit_code, exit_error[0], exit_error[1], cmd
					)
				)

		# super instead
		else:
			return super(executer_vps, self).get_wrap_log(exit_code, cmd)

# }}}
