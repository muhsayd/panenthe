#!/usr/bin/env python

# Panenthe: glob.py
# Defines code that is executed globally.

##

from ConfigParser import SafeConfigParser
import os, sys

# require Python 2.4
if sys.version_info[:2] < (2, 4):
	print "Python 2.4 or greater is required. " + \
	      "(Currently running %d.%d)" % sys.version_info[:2]
	sys.exit(-1)

# read the user's config file just for root_dir
config = SafeConfigParser()
config.read("/etc/panenthe.conf")

# determine root_dir
root_dir = config.get("main", "root_dir")

# read our defaults
config.read(os.path.join(root_dir, "shared/etc/sys.conf"))

# read user's config file again to overwrite any defaults redefined
config.read("/etc/panenthe.conf")

# some functions for good use {{{

def getfile(*paths):
	return os.path.join(root_dir, *paths)

# }}}
