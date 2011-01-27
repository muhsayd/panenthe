#!/usr/bin/env python

# Panenthe: backend.py
# Gateway from PHP to Python for executing commands.

import glob
import errors

##

import md5
import os
import signal
import subprocess
import sys
import time
from select import select

class backend(object):
	@staticmethod
	def generate_randstr():
		return md5.md5(str(time.time())).hexdigest()

	@staticmethod
	def execute(cls, cmd, args, debug_mode = False):
		try:
			exec("import %s" % cls)

			if debug_mode:
				exec("tmp = %s.%s(\"%s\", %s)" % (cls, cls, cmd, args))
				ret = (tmp, tmp.execute())

			# not debug mode
			else:
				try:
					exec("tmp = %s.%s(\"%s\", %s)" % (cls, cls, cmd, args))
					ret = (tmp, tmp.execute())

				# catch uncaught exceptions
				except Exception:
					ret = (None, errors.throw(errors.PY_UNCAUGHT_EXCEPTION))

		# no module with that name
		except ImportError:
			ret = (None, errors.throw(errors.BACKEND_INVALID_INPUT))

		return ret

if __name__=="__main__":
	# different modes
	debug_mode = False
	queue_mode = False
	q_deps = []
	extra_output = []

	try:
		# handle switches
		while sys.argv[3][0] == "-":
			# debug mode
			if sys.argv[3] == "-d":
				debug_mode = True
			# queue_mode mode
			if sys.argv[3] == "-q":
				queue_mode = True
				sys.argv.pop(3)
				q_deps.append(sys.argv[3])

			# clear switch
			sys.argv.pop(3)

		# argument mapping
		cls = sys.argv.pop(1)
		cmd = sys.argv.pop(1)
		args = sys.argv.pop(1)

	# die if no more arguments
	except IndexError:
		print errors.throw(errors.BACKEND_INVALID_INPUT)[0]
		sys.exit(1)

	# queue mode
	if queue_mode:

		# look for panenthe_q.py running
		q = os.popen("/usr/bin/env ps -o pid,command -C python", "r")
		ps_lines = q.readlines()

		# generate id
		id = backend.generate_randstr()

		is_running = False
		for line in ps_lines:
			if line.find("panenthe_q.py") != -1:
				pid = int(line.strip().split(" ")[0])
				fifo_path = glob.getfile("srv/tmp", "panenthe_q.%s" % pid)

				# check fifo to see if it exists
				try:
					os.stat(fifo_path)
					# fifo exists, yay!
					is_running = True
					break

				# no fifo... kill the process because it's rogue... like
				# Jack Bauer.  This only applies to panenthe_q.py processes, so
				# be careful.
				except OSError:
					os.kill(pid, signal.SIGTERM)

		if is_running:
			# if it is, pass this info to panenthe_q.py through the fifo
			fifo_path = glob.getfile("srv/tmp", "panenthe_q.%s" % pid)

			# write to FIFO
			# MUST USE POSIX HERE.  Python's stuff is too crappy for FIFO work.
			q_fifo = os.open(fifo_path,
				os.O_APPEND | os.O_WRONLY | os.O_NONBLOCK
			)
			os.write(q_fifo,
				"%s\0%s\0%s\0%s\0%s\n" % (
					id, " ".join(q_deps), cls, cmd, args
				)
			)
			os.close(q_fifo)

			# success, now leave
			error_code = errors.throw(errors.ERR_QUEUED)

			# output
			print error_code[0]
			print id

		# not running, so start it
		else:
			proc = subprocess.Popen("/usr/bin/env python %s %s %s %s \"%s\"" % (
				glob.getfile("shared/drivers/panenthe_q.py"),
				id, cls, cmd, args.replace("\"", "\\\"")
				), shell = True,
				stdout = subprocess.PIPE, stderr = subprocess.STDOUT
			)
			proc.wait() # wait until it quits
			proc.poll() # get information

			# get output
			q_ret = []
			(first,_,_) = select([proc.stdout], [], [], 2)
			data = None
			while first != [] and data != "":
				data = proc.stdout.readline()
				q_ret.append(data)
				(first,_,_) = select([proc.stdout], [], [], 2)
			proc.stdout.close()

			# success output
			if q_ret[0].strip() == errors.ERR_QUEUED[0]:
				error_code = errors.throw(errors.ERR_QUEUED)
				extra_output.append(q_ret[1]) # id of queued command

			# failed output
			else:
				error_code = errors.throw(errors.ERR_QUEUE_FAILED)

			q_ret[0] = error_code[0]
			for line in q_ret:
				print line

	# just execute
	else:
		(control, error_code) = backend.execute(
			cls, cmd, args, debug_mode
		)

		# normally we would want error_code to always output an error, but if it
		# returns None, it might be for a reason.
		# (for instance, masterctl cron_daily())
		if error_code != None and error_code[1] != None:
			#print error_code # debug
			print error_code[0]
