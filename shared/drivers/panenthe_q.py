#!/usr/bin/env python

# Panenthe: panenthe_q.py
# Command queueing daemon.

import glob
from backend import backend
import errors
import q_messages
import php

##

import os
import signal
import sys

class q(object):
	def __init__(self):
		self.q = {}
		self.fail = []

	# logging to stdout since it is redirected for daemon
	def log(self, message):
		if sys.stdout != sys.__stdout__:
			self.log_raw("%s\n\n" % message)

	def log_raw(self, message):
		sys.stdout.write(message)
		sys.stdout.flush()

	# push something onto queue
	def push(self, id, deps, cls, cmd, args):
		self.q[id] = {
			'deps': deps,
			'cls': cls,
			'cmd': cmd,
			'args': args
		}

	def execute(self, id):
		# get cmd dict
		q_item = self.q.pop(id)

		self.log("q.execute(id): %s" % id)

		# execute deps first
		for dep in q_item['deps']:
			if self.q.has_key(dep):
				self.log("q.execute(...): dependency: %s" % dep)
				self.execute(dep)

		# fail if any dependency failed
		for dep in q_item['deps']:
			try:
				self.fail.index(dep)
				self.log("q.execute(...): dependency failed: %s" % dep)
				return (None, errors.throw(errors.ERR_QUEUE_DEP_FAILED))
			except ValueError: pass

		# continue with normal execution, set up the attributes
		cls = q_item['cls']
		cmd = q_item['cmd']
		args = q_item['args']

		# pre-execute log
		self.log("backend.execute(cls, cmd, args, True): %s %s %s True" %
			(cls, cmd, str(args))
		)

		# execute (debug mode on)
		(control, error_code) = backend.execute(cls, cmd, args, True)

		# post-execute log
		self.log("(control, error_code) = backend.execute(...): %s %s" %
			(str(control), str(error_code))
		)

		# log to event table in DB
		self.event_log(cls, cmd, control, error_code)

		# append to fail list if it didn't succeed
		if error_code != errors.ERR_SUCCESS:
			self.fail.append(id)

		return (control, error_code)

	def event_log(self, cls, cmd, control, error_code):
		# entity has a driver
		try:
			control.entity.driver

			# read driver config for messages
			q_messages.read_driver(control.entity.driver)

			# add drivers path to system path for import
			sys.path.append(glob.getfile("drivers", control.entity.driver))

			# import, and make sure it exists
			try:
				exec("from %s_q_after_exec import %s_q_after_exec" % (
					control.entity.driver, control.entity.driver
				))
				exec("tmp = %s_q_after_exec()" % control.entity.driver)
				exec("after_ret = tmp.execute(cls, cmd, control.entity)")
			except ImportError:
				after_ret = errors.throw(DRIVER_NO_AFTER_EXEC)

		# entity has no driver
		except AttributeError:
			# queue_mode afterwards execution
			from q_after_exec import q_after_exec
			tmp = q_after_exec()
			after_ret = tmp.execute(cmd, cls, control.entity)

		# get event message
		event_msg = q_messages.get(cls, cmd, control.entity, error_code)

		# insert event if there is a message
		if event_msg != None:
			php.db_update("events", "insert", event_msg, error_code[0])

	def daemon(self, fifo_name):
		# open fifo at start so the queue commands can return
		# MUST USE POSIX HERE.  Python's stuff is too crappy for FIFO work.
		self.log("q.daemon(...): fifo start: %s" % fifo_name)
		fifo = os.open(fifo_name, os.O_RDONLY | os.O_NONBLOCK)

		while self.q.keys() != []:
			self.log("q.daemon(...): key count: %d" % len(self.q.keys()))
			# loop through known keys and execute them
			for key in self.q.keys():

				# store cmd since self.q gets the command popped off in execute
				try:
					cmd = self.q[key]
				except KeyError: continue # the command might have been executed
				                          # as a dependency already

				# execute key
				self.log("q.daemon(...): execute(key): %s(%s)" % (key, cmd))
				(control, error_code) = self.execute(key)

				# log end of process
				self.log(
					"q.daemon(...): q.execute(...): done: %s(%s): (%s, %s)" %
						(key, cmd, str(control), str(error_code))
				)

			# all done, now let's check the fifo for more!
			chunk = None
			fifo_raw = ""
			while chunk != "":
				chunk = os.read(fifo, 8192)
				fifo_raw += chunk

			# log
			self.log("q.daemon(...): fifo found: %s" % fifo_raw)

			# parse that raw fifo goodness
			fifo_cmds = fifo_raw.split("\n")
			for cmd in fifo_cmds:
				cmd_arr = cmd.strip().split("\0")

				# not enough arguments
				if len(cmd_arr) != 5:
					continue

				# log it
				self.log("q.daemon(...): command array: %s" % str(cmd_arr))

				# args
				id = cmd_arr.pop(0)
				deps = cmd_arr.pop(0).split(" ")
				cls = cmd_arr.pop(0)
				cmd = cmd_arr.pop(0)
				args = cmd_arr.pop(0)

				# push fifo data onto queue
				self.push(id, deps, cls, cmd, args)
				self.log(
					"q.push(id, deps, cls, cmd, args): " + \
					"%s %s %s %s %s" % (id, deps, cls, cmd, str(args))
				)

		# all done, close fifo
		os.close(fifo)

if __name__=="__main__":
	# argument mapping
	id = sys.argv.pop(1)
	cls = sys.argv.pop(1)
	cmd = sys.argv.pop(1)
	args = sys.argv.pop(1)

	# create queue object
	queue = q()

	# push initial command onto queue
	queue.push(id, [], cls, cmd, args)

	# child daemonizes and executes until it's out of things to run
	if os.fork() == 0:

		# get pid
		pid = os.getpid()

		# make fifo
		fifo_name = glob.getfile("srv/tmp", "panenthe_q.%s" % pid)
		os.mkfifo(fifo_name)

		# redirect stdout and stderr to files
		sys.stdout = open(glob.getfile("logs/panq_stdout"), "a")
		sys.stderr = open(glob.getfile("logs/panq_stderr"), "a")

		# output that a new process has started
		sys.stdout.write("\n\n\n--\n\n\nnew queue pid: %d\n\n" % pid)
		sys.stdout.flush()

		# catch uncaught exceptions
		try:
			# start daemon
			queue.daemon(fifo_name)
		except Exception, e:
			# insert event saying queue failed
			php.db_update(
				"events", "insert",
				"The command queue failed with uncaught exception: %s" % e,
				errors.ERR_QUEUE_IDLE[2]
			)

		# unlink fifo
		os.unlink(fifo_name)

		# insert event saying queue is idle
		php.db_update(
			"events", "insert", "The command queue is now idle.",
			errors.ERR_QUEUE_IDLE[2]
		)

		# process ends
		sys.stdout.write("\n\npid quit: %d\n" % pid)

		# exit
		sys.exit(0)

	# parent tells PHP what the id of the command is
	else:
		pid = os.getpid()
		print errors.throw(errors.ERR_QUEUED)[0]
		print id

		# flush output
		sys.stdout.flush()
		#sys.stdout.close() # should I? hmm... right now I think not.

		# kill self because the parent refuses to close
		os.kill(os.getpid(), signal.SIGTERM)
		sys.exit(0) # why not? :)
		# Seriously this is all PHPs fault... The parent should close and that
		# should be it, but NO.  PHP says "oooo, I'm a little bitch and want to
		# keep the parent open if the child is still there!! because in my
		# little fantasy land parents never die before their children!!!! I'm
		# just an old fart and I have to pretend that as long as my children are
		# alive I am too!!!!!  I DON'T WANT TO DIE PLEASE FOR THE LOVE OF GOD I
		# WANT AN AFTERLIFE!!! PLEASE KEEP ME ALIVE MY CHILD OKAY OKAY OKAY....
		# calm down... okay... NOOOOOOO I CAN'T DIE!!!! I'm SPECIAL!! MY
		# CHILDREN SUCK AND THEY CAN'T LIVE WITHOUT ME SO I'M GOING TO TAKE THEM
		# DOWN WHEN I DIE OR THEY WILL HAVE TO KEEP ME ALIVE UNTIL THEY'RE
		# DEAD!!!!!" -- sorry had to vent... I've spent at least 8 hours over
		# this bug altogether. I wrote over 100 lines of debug code and ended up
		# with a result of about 5 lines of production code.  Fuck PHP.
