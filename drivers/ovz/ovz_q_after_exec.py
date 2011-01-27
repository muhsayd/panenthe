#!/usr/bin/env python

# Panenthe: ovz_q_after_exec.py
# Defines commands to be executed after a queued process is over for OpenVZ.

from q_after_exec import q_after_exec

##

# nothing special for OpenVZ yet
class ovz_q_after_exec(q_after_exec):
	pass
