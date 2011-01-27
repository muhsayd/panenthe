#!/usr/bin/env python

# Panenthe: xen_q_after_exec.py
# Defines commands to be executed after a queued process is over for Xen.

from q_after_exec import q_after_exec

##

# nothing special for Xen yet
class xen_q_after_exec(q_after_exec):
	pass
