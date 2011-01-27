#!/usr/bin/env python

# Panenthe: errors.py
# Loads errors and provides an interface for throwing them.

##

import glob

# error severities
SEVERITY_NORMAL = "NORMAL"
SEVERITY_NOTICE = "NOTICE"
SEVERITY_WARNING = "WARNING"
SEVERITY_FATAL = "FATAL"

# get error codes
error_file = open(glob.getfile("shared/etc/errors"), "r")
error_lines = error_file.readlines()
error_file.close()

# loop lines and define all errors
for line in error_lines:
	# deal with comments
	if line[0]=="#": continue

	error_split = line.split("|")
	if len(error_split) > 1:
		error_code = error_split[0].strip()
		error_severity = error_split[2].strip()
		error_name = error_split[2].strip()
		exec("%s = (\"%s\", \"%s\", \"%s\")" % (
			error_name, error_code, error_severity, error_name
		))

def throw(error):
	# don't log successes
	success_codes = glob.config.get("main", "success_codes").split("|")
	if len(success_codes) > 1:
		for code in success_codes:
			code = code.strip()
			if error[2] == code:
				return error

	# get time
	import time
	time = time.strftime(glob.config.get("errors", "date_format"))

	# write to system log
	sys_error = open(
		glob.getfile(glob.config.get("errors", "file_sys_error")), "a"
	)
	sys_error.write("%s:PY:%s:%s\n" % (time, error[0], error[1]))
	sys_error.close()

	return error
