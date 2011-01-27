#!/usr/bin/env python

# Panenthe: xen_exit_codes.py
# Translates Xen exit codes to errors

import errors

##

def translate(code, context = None):
	codes = {
		0: errors.ERR_SUCCESS,
		256: errors.XEN_DAEMON_NOT_RUNNING,
	}

	contexts = {
		'create': {
			1: errors.XEN_ERR_UNKNOWN,
		},
		'shutdown': {
			1: errors.XEN_DOMAIN_DOESNT_EXIST,
		},
	}

	# contextual exit codes
	try:
		return errors.throw(contexts[context][code])
	except (IndexError, KeyError): pass

	# regular exit codes
	try:
		codes[code]
	except KeyError:
		return errors.throw(errors.XEN_ERR_UNKNOWN)

	return errors.throw(codes[code])
