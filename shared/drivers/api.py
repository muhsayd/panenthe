#!/usr/bin/env python

# Panenthe: api.py
# Interfaces with the database API

import glob
import errors
from execute import *

##

import os
import urllib

class api_call(object):

	def __init__(self, call_name, params):
		self.cmd_params = ""
		for key, value in params.iteritems():
			self.cmd_params += " --%s %s" % (key, value)

		self.call_path = glob.getfile("ui/api", call_name + ".php")

	# execute API
	def execute(self):
		if not os.path.isfile(self.call_path):
			return errors.throw(errors.API_UNKNOWN_FUNCTION)

		(self.exit_code, self.stdout_list, self.stderr_list) = \
			execute("%s %s -n%s" % (
					glob.config.get("paths", "php"),
					self.call_path,
					self.cmd_params
				)
			)

		# handle errors
		if self.exit_code != 0:
			return errors.throw(errors.API_ERR_UNKNOWN)

		# create plain text variables
		self.stdout = "\n".join(self.stdout_list)
		self.stderr = "\n".join(self.stderr_list)

		# success
		return errors.throw(errors.ERR_SUCCESS)

	# retrieve output
	def output(self):
		ret = []

		out_array = self.stdout.split("###")

		# loop through return output
		for out in out_array:
			if out == "": continue
			ret.append({})

			attr_list = out.split("&")

			# loop through attribute fields
			for attr in attr_list:
				key, value = urllib.splitvalue(attr)

				# clean up key
				if key == None:
					key = ""
				else:
					key = urllib.unquote(key).replace("+", " ")

				# clean up value
				if value == None:
					value = ""
				else:
					value = urllib.unquote(value).replace("+", " ")

				# determine if it is a list, and if so, treat it as one
				pos = key.find("[")
				if pos != -1 and key[-1:] == "]":
					key = key[:pos]
					# create the list if not created already
					if not ret[-1].has_key(key):
						ret[-1][key] = []

				# try to cast as an integer, otherwise settle with a string
				# otherwise make it a string
				try:
					value = int(value)
				except ValueError: pass

				# store it as a list if necessary, otherwise just plain store it
				try:
					ret[-1][key].append(value)
				except (KeyError, AttributeError):
					ret[-1][key] = value

		# return and quit
		return ret
