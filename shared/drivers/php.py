#!/usr/bin/env python

# Panenthe: php.py
# Calls to PHP.

import glob

##

from execute import execute

def db_update(*args):
	return execute("%s %s \"%s\"" % (
			glob.config.get("paths", "php"),
			glob.getfile("shared/bridge", "db_update.php"),
			"\" \"".join(args)
		)
	)
