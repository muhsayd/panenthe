#!/usr/bin/env python

# Panenthe: q_after_exec.py
# Defines commands to be executed after a queued process is over.

import errors
import glob
import php

##

class q_after_exec(object):
	def execute(self, cls, cmd, entity):
		# has an after event defined
		try:
			exec("ret = self.%s__%s(entity)" % (cls, cmd))
			return ret

		# has no after event
		except AttributeError:
			return errors.throw(errors.ERR_SUCCESS)

	# delete VPS on destroy if not forced (force will delete before the process
	# makes it to this)
	def vpsctl__destroy(self, entity):
		if not entity.force:
			php.db_update("vps", "delete", str(entity.vps_id))
