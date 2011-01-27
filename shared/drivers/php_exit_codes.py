#!/usr/bin/env python

# Panenthe: ovz_exit_codes.py
# Translates OpenVZ exit codes to errors

import errors

##

def translate(code):
	codes = {
		0: errors.ERR_SUCCESS,
		1: errors.PHP_DATABASE_FAIL,
		127: errors.PHP_FILE_NOT_FOUND,
	}

	try:
		codes[code]
	except KeyError:
		return errors.throw(errors.PHP_ERR_UNKNOWN)

	return errors.throw(codes[code])
