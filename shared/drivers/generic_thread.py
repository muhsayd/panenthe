#!/usr/bin/env python

# Panenthe: generic_thread.py
# Defines a generic thread class.

from threading import Thread

class generic_thread(Thread):
	def __init__(self, function, args = None):
		Thread.__init__(self)
		self.function = function
		self.args = args

	def run(self):
		if self.args:
			self.ret = self.function(*args)
		else:
			self.ret = self.function()
