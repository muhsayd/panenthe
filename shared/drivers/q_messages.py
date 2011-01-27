#!/usr/bin/env python

# Panenthe: q_messages.py
# Loads and compiles queue event messages.

import glob
import errors

##

import ConfigParser
import os

# read messages from config
messages = ConfigParser.SafeConfigParser()
messages.read(glob.getfile("shared/etc/q_messages.conf"))

# read the driver's messages to overwrite
def read_driver(driver):
	messages.read(
		glob.getfile("drivers", driver, "%s_q_messages.conf" % driver)
	)

# get queue message
def get(cls, cmd, entity, code):

	# get section
	if code == errors.ERR_SUCCESS:
		section = "success"
	else:
		section = "fail"

	try:
		# get message template
		msg = messages.get(section, "%s__%s" % (cls, cmd))

		# templating replacements {{{

		# dns
		if msg.find("__dns__") != -1:
			msg = msg.replace("__dns__", entity.dns)

		# hostname
		if msg.find("__hostname__") != -1:
			msg = msg.replace("__hostname__", entity.hostname)

		# ip
		if msg.find("__ip__") != -1:
			msg = msg.replace("__ip__", entity.ip)

		# ost_name
		if msg.find("__ost_name__") != -1:
			msg = msg.replace("__ost_name__", entity.ost_name)

		# password
		if msg.find("__password__") != -1:
			msg = msg.replace("__password__", entity.password)

		# real_id
		if msg.find("__real_id__") != -1:
			msg = msg.replace("__real_id__", str(entity.real_id))

		# server_hostname
		if msg.find("__server_hostname__") != -1:
			msg = msg.replace("__server_hostname__", entity.server['hostname'])

		# service
		if msg.find("__service__") != -1:
			msg = msg.replace("__service__", entity.service)

		# username
		if msg.find("__username__") != -1:
			msg = msg.replace("__username__", entity.username)

		# }}}

	# nothing to output
	except ConfigParser.NoOptionError:
		msg = None

	return msg
