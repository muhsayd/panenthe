#!/usr/bin/env python

# Panenthe: ovz_exit_codes.py
# Translates OpenVZ exit codes to errors

import errors

##

def translate(code):
	codes = {
		0: errors.ERR_SUCCESS,
		8: errors.OVZ_NOT_RUNNING,
		9: errors.OVZ_CURRENTLY_LOCKED,
		14: errors.OVZ_CONFIG_DOES_NOT_EXIST,
		20: errors.OVZ_BAD_COMMAND,
		21: errors.OVZ_INVALID_INPUT,
		32: errors.OVZ_ALREADY_RUNNING,
		41: errors.OVZ_CONTAINER_MOUNTED,
		44: errors.OVZ_PRIVATEAREA_EXISTS,
		78: errors.OVZ_ADDRESS_IN_USE,
		91: errors.OVZ_FILE_NOT_FOUND,
		255: errors.OVZ_SSH_CONNECTION_FAILURE
	}

	try:
		codes[code]
	except KeyError:
		return errors.throw(errors.OVZ_ERR_UNKNOWN)

	return errors.throw(codes[code])
