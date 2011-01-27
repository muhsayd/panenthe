#!/usr/bin/env python

# Panenthe: attrdict.py
# Defines a class that converts a dictionary input into attributes

##

class attrdict(object):
	def __init__(self, dict):
		for attr in dict:
			exec("self.%s = dict[attr]" % attr)
		self.dict = dict

	# return dict
	def get_dict(self):
		return self.dict

	# require attributes
	def require(self, requirements):
		if isinstance(requirements, str):
			try:
				eval("self.%s" % requirements)
			except AttributeError:
				return False
			return True

		else:
			for i in requirements:
				try:
					eval("self.%s" % i)
				except AttributeError:
					return False
			return True

	# require dictionary keys
	def require_dict(self, dict, requirements):
		if isinstance(requirements, str):
			try:
				eval("dict[\"%s\"]" % requirements)
			except KeyError:
				return False
			return True

		else:
			for i in requirements:
				try:
					eval("dict[\"%s\"]" % i)
				except KeyError:
					return False
			return True

	# force casting as integer
	def force_cast_int(self, castees):
		return self.force_cast_type(int, castees)
	def force_cast_int_dict(self, dictnames, castees):
		return self.force_cast_type_dict(int, dictnames, castees)

	# force casting as boolean
	def force_cast_bool(self, castees):
		return self.force_cast_type(bool, castees)
	def force_cast_bool_dict(self, dictnames, castees):
		return self.force_cast_type_dict(bool, dictnames, castees)

	# force casting as a type
	def force_cast_type(self, vartype, castees):
		if isinstance(castees, str):
			exec("self.%s = vartype(self.%s)" % (castees, castees))
			exec("self.dict[\"%s\"] = self.%s" % (castees, castees))
		else:
			for i in castees:
				exec("self.%s = vartype(self.%s)" % (i, i))
				exec("self.dict[\"%s\"] = self.%s" % (i, i))

	# force casting as a type on dictionary
	def force_cast_type_dict(self, vartype, dictnames, castees):
		# parse dictname if list
		# ["a", "b", "c"] produces the following string for dictobjectname:
		#   a["b"]["c"]
		# and for dictname:
		#   a"]["b"]["c
		if isinstance(dictnames, list):
			dictname = "\"][\"".join(dictnames)
			dictobjectname = \
				dictnames[0] + "[\"" + "\"][\"".join(dictnames[1:]) + "\"]"

		# not a list, just a string
		else:
			dictname = dictnames
			dictobjectname = dictnames

		# deal with casting for a single item
		if isinstance(castees, str):
			exec("self.%s[\"%s\"] = vartype(self.%s[\"%s\"])" % (
				dictobjectname, castees,
				dictobjectname, castees
			))
			exec("self.dict[\"%s\"][\"%s\"] = self.%s[\"%s\"]" % (
				dictname, castees,
				dictobjectname, castees
			))

		# deal with casting for multiple items
		else:
			for i in castees:
				exec("self.%s[\"%s\"] = vartype(self.%s[\"%s\"])" % (
					dictobjectname, i,
					dictobjectname, i
				))
				exec("self.dict[\"%s\"][\"%s\"] = self.%s[\"%s\"]" % (
					dictname, i,
					dictobjectname, i
				))
