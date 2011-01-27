#!/usr/bin/env python

# Panenthe: xen_core.py
# Xen driver core.

import glob
import errors

import os
import re
import tempfile

import php
import vps
from execute import *
from generic_thread import generic_thread
import php_exit_codes
import xen_exit_codes


class xen_core(vps.vps):

	def __init__(self, dict):
		ret = super(xen_core, self).__init__(dict)
		if ret: return ret

		# Xen requires this; value: (WINDOWS|LINUX|OTHER)
		if not self.require("os_type"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# cast real_id as int if available
		try:
			self.real_id
			self.force_cast_int("real_id")
		except AttributeError: pass

		# store mounts for __quit_clean()
		self.mounts = []

	# static methods {{{

	@staticmethod
	def get_new_real_id(vps):
		# start at 100
		real_id = 100

		found = False
		while not found:
			# check if real_id has storage used
			(exit_code,_,_) = vps.do_execute(
				"test -d %s" % os.path.join(
					glob.config.get("xen", "root_domain_dir"), str(real_id)
				)
			)

			# if it does, keep looking
			if exit_code == 0:
				real_id += 10

			# otherwise we are done
			else:
				found = True

		return real_id

	# }}}

	# meta {{{

	def next_id(self):
		ret = super(xen_core, self).next_id()
		if ret: return ret

		# real_id is fine
		try:
			self.real_id

		# need new real_id
		except AttributeError:
			ret = xen_core.get_new_real_id(self)

			# fail
			if not type(ret) == int:
				return errors.throw(errors.XEN_GENERATE_REAL_ID)

			# success
			self.real_id = ret

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"vps", "update_real_id",
			str(self.vps_id), str(self.real_id)
		)

		return php_exit_codes.translate(php_exit_code)

	def __get_config_path(self, hostname = None):
		vm_directory = self.__get_vm_directory()

		# get hostname to use
		if hostname == None:
			hostname = self.hostname

		return os.path.join(
			vm_directory, "%s.cfg" % executer.escape(hostname)
		)

	def __get_disk_img_path(self):
		vm_directory = self.__get_vm_directory()
		return os.path.join(vm_directory, "sda1.img")

	def __get_ost_path(self, ost_file = None):
		vm_directory = self.__get_vm_directory()

		# get ost to use
		if ost_file == None:
			ost_file = self.ost

		return os.path.join(
			glob.config.get("paths", "ost_xen"), executer.escape(ost_file)
		)

	def __get_swap_img_path(self):
		vm_directory = self.__get_vm_directory()
		return os.path.join(vm_directory, "swap.img")

	def __get_vm_directory(self):
		return os.path.join(
			glob.config.get("xen", "root_domain_dir"), str(self.real_id)
		)

	def __get_xentop_info(self):
		try:
			self.xentop_info
			return errors.throw(errors.ERR_SUCCESS)
		except AttributeError: pass

		(exit_code,stdout,_) = self.do_execute("xentop -b -i 1")

		if exit_code != 0:
			return xen_exit_codes.translate(exit_code)

		# get this VPS' information
		line = -1
		split_info = None
		for i in xrange(4, len(stdout)): # domain info starts on line 5
			info = re.sub("[ ]+", " ", stdout[i].strip())
			split_info = info.split(" ")
			if split_info[0] == str(self.real_id):
				line = i
				break

		# cache it locally
		if line == -1:
			self.xentop_info = -1
		else:
			self.xentop_info = split_info

		return errors.throw(errors.ERR_SUCCESS)

	def __quit_clean(self, mount_dir = None):
		worst_exit = errors.ERR_SUCCESS
		for mount in self.mounts:
			(exit_code,_,_) = self.do_execute("umount \"%s\"" % mount)
			if exit_code != 0:
				worst_exit = errors.throw(errors.XEN_UMOUNT_LOOP)

		if mount_dir != None:
			os.rmdir(mount_dir)
		self.mounts = []
		return worst_exit

	def __mount_loopback(self, image_path):
		# create loop directory mount point
		loop_dir = tempfile.mkdtemp(prefix = "panenthe")
		self.do_execute("mkdir -p \"%s\"" % loop_dir)

		# mount loopback file
		self.mounts.append(loop_dir)
		(exit_code,_,_) = self.do_execute("mount -o loop \"%s\" \"%s\"" % (
			image_path, loop_dir
		))

		# handle errors
		if exit_code != 0:
			self.__quit_clean(loop_dir)
			return False

		return loop_dir

	def __umount_sanity(self, image_path):
		# umount the image if a failure occurred elsewhere (sanity)
		exit_code = 0
		max = int(glob.config.get("xen", "umount_tries"))
		while exit_code == 0 and max > 0:
			(exit_code,_,_) = self.do_execute(
				"umount \"%s\"" % image_path
			)
			max -= 1

	def __debian_net_reset(self, loop_dir):
		# SEE FOOTNOTE 1
		self.do_execute("echo \"auto lo\" > \"%s\"" % os.path.join(
			loop_dir, glob.config.get("paths", "network_debian")[1:]
		))
		self.do_execute("echo \"iface lo inet loopback\" >> \"%s\"" %
			os.path.join(
				loop_dir, glob.config.get("paths", "network_debian")[1:]
			)
		)

	# }}}

	# database {{{

	def lock(self):
		ret = super(xen_core, self).lock()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	def unlock(self):
		ret = super(xen_core, self).unlock()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# management {{{

	def create(self):
		ret = super(xen_core, self).create()
		if ret: return ret

		# required to know what kernel to boot to
		if not self.require("kernel"):
			return errors.throw(errors.BACKEND_INVALID_INPUT)

		# get relevant paths
		config_path = self.__get_config_path()
		disk_img_path = self.__get_disk_img_path()
		ost_path = self.__get_ost_path()
		swap_img_path = self.__get_swap_img_path()
		vm_directory = self.__get_vm_directory()

		# make sure path exists
		self.do_execute("mkdir -p \"%s\"" % vm_directory)

		# config {{{

		# kernel setup {{{

		config_data = "# Kernel Setup\n"

		if self.os_type == "WINDOWS" or self.os_type == "OTHER":
			config_data += \
				"kernel = \"/usr/lib/xen/boot/hvmloader\"\n" + \
				"builder = \"hvm\"\n" + \
				"device_model = \"/usr/lib/xen/bin/qemu-dm\"\n"

		else:
			config_data += \
				"kernel = \"/boot/%s\"\n" % executer.escape(self.kernel)

		config_data += "\n"

		# }}}

		# memory {{{

		# same for both
		memory = self.g_mem / 1024
		config_data += \
			"# Memory\n" + \
			"memory = \"%s\"\n" % memory + \
			"\n"

		# }}}

		# disk {{{

		config_data += "# Disk\n"

		if self.os_type == "WINDOWS" or self.os_type == "OTHER":
			config_data += \
				"disk = [\n" + \
				"\"file:%s,ieomu:hda,w\",\n" % disk_img_path + \
				"\"file:%s,hdc:cdrom,r\",\n" % ost_path + \
				"]\n\n"
				#"\"file:windows,ioemu,hdc:cdrom,r\"\n" + \

		else:
			config_data += \
				"disk = [\n" + \
				"\"file:%s,sda1,w\",\n" % disk_img_path

			# add swap if necessary
			if self.swap_space != 0:
				config_data += "\"file:%s,sda2,w\"\n" % swap_img_path

			config_data += "]\n\n"

		# }}}

		# name and hostname {{{

		config_data += \
			"# container name\n" + \
			"name = \"%d\"\n" % self.real_id + \
			"hostname = \"%s\"\n" % self.hostname + \
			"\n"

		# }}}

		# networking {{{

		config_data += \
			"# Networking\n" + \
			"vif = [\"type=ieomu, bridge=xenbr0\"]\n" + \
			"\n"

		# }}}

		# VNC {{{

		config_data += "# VNC\n"

		if self.os_type == "WINDOWS" or self.os_type == "OTHER":
			config_data += \
				"vnc = 1\n" + \
				"vncdisplay=1\n" + \
				"vnclisten=\"0.0.0.0\"\n" + \
				"vncpasswd=\"%d\"\n" % self.real_id + \
				"\n"
		else:
			config_data += \
				"vfb = [\"type=vnc," + \
					"vncdisplay=18," + \
					"vnclisten=0.0.0.0," + \
					"vncpasswd=%d" % self.real_id + \
				"\"]\n\n"
		#	 TODO: self.vncpasswd not self.real_id
		#	 TODO: vncdisplay not 1
		#	"vncdisplay = 1\n" % self.real_id + \ # TODO

		# }}}

		# behavior settings {{{

		config_data += "# Behavior Settings\n"

		if self.os_type == "WINDOWS" or self.os_type == "OTHER":
			config_data += \
				"boot = \"d\"\n" + \
				"sdl = 0\n"

		else:
			config_data += \
				"root = \"/dev/sda1\"\n" + \
				"extra = \"fastboot\"\n"

		config_data += "\n"

		# }}}

		# event settings {{{
		config_data += \
			"# Event Settings\n" + \
			"on_poweroff = 'destroy'\n" + \
			"on_reboot = 'restart'\n" + \
			"on_crash = 'restart'\n" + \
			"\n"

		# write config to temp file
		config_fd = tempfile.mkstemp(prefix = "panenthe")
		config_fp = open(config_fd[1], 'w')
		config_fp.write(config_data)
		config_fp.close()

		# copy config over
		execute(
			"scp -i %s -P %d %s root@%s:%s" % (
				glob.config.get("paths", "master_private_key"),
				self.server['port'],
				config_fd[1],
				executer.escape(self.server['ip']), config_path
			)
		)

		# remove temp file
		os.unlink(config_fd[1])

		# }}}

		# create null disk image
		(exit_code,_,_) = self.do_execute(
			"dd if=/dev/zero of=\"%s\" bs=%d count=0 seek=1024" % (
				disk_img_path, self.disk_space
			)
		)

		if exit_code != 0:
			return errors.throw(errors.XEN_DISK_CREATION)

		# sync server OST file
		server = self.get_server()
		server.file_sync(ost_path)

		# linux only disk finalization
		if self.os_type == "LINUX":

			# make disk filesystem
			(exit_code,_,_) = self.do_execute(
				"mkfs.ext3 -F \"%s\"" % disk_img_path
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_DISK_CREATION)

			# only make swap if not zero
			if self.swap_space != 0:
				# create null swap image
				(exit_code,_,_) = self.do_execute(
					"dd if=/dev/zero of=\"%s\" bs=%d count=0 seek=1024" % (
						swap_img_path, self.swap_space
					)
				)

				if exit_code != 0:
					return errors.throw(errors.XEN_SWAP_CREATION)

				# make swap
				(exit_code,_,_) = self.do_execute(
					"mkswap \"%s\"" % swap_img_path
				)

				if exit_code != 0:
					return errors.throw(errors.XEN_SWAP_CREATION)

			# mount loopback device
			loop_dir = self.__mount_loopback(disk_img_path)

			if not loop_dir:
				self.__quit_clean()
				return errors.throw(errors.XEN_MOUNT_LOOP)

			# extract OST
			(exit_code,_,_) = self.do_execute("tar xzf \"%s\" -C \"%s\"" % (
				ost_path, loop_dir
			))

			if exit_code != 0:
				self.__quit_clean(loop_dir)
				return errors.throw(errors.XEN_DISK_FINALIZATION)

			# enter swap into the fstab
			(exit_code,_,_) = self.do_execute(
				"echo \"/dev/sda2 none swap defaults 0 0\" >> %s" %
					os.path.join(loop_dir, "etc", "fstab")
			)

			if exit_code != 0:
				self.__quit_clean(loop_dir)
				return errors.throw(errors.XEN_DISK_FINALIZATION)

			# get distro
			self.get_distro(loop_dir)

			# if debian, reset the IP config file
			if self.distro == "debian":
				self.__debian_net_reset(loop_dir)

			# if redhat, remove any extraneous network config files
			# SEE FOOTNOTE 1
			elif self.distro == "redhat":
				(exit_code,_,_) = self.do_execute(
					"rm -f \"%s\"*" % os.path.join(
						loop_dir,
						glob.config.get("paths", "network_redhat_dir")[1:],
						"ifcfg-eth"
					)
				)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_DISK_FINALIZATION)

			# clean up
			self.__quit_clean()

		return errors.throw(errors.ERR_SUCCESS)

	def destroy(self):
		ret = super(xen_core, self).destroy()
		if ret: return ret

		# don't really want destroy here, needs to wait for it to end
		(exit_code,_,_) = self.do_execute(
			"%s shutdown %d -w" % (
				glob.config.get("paths", "xm"),
				self.real_id
			)
		)

		if exit_code != 0 and exit_code != 1:
			# ignore "Domain doesn't exist" error for shutdown
			ret = xen_exit_codes.translate(exit_code, "shutdown")
			if ret != errors.XEN_DOMAIN_DOESNT_EXIST:
				return errors.throw(ret)

		(exit_code,_,_) = self.do_execute(
			"rm -rf %s" % os.path.join(
				glob.config.get("xen", "root_domain_dir"),
				str(self.real_id)
			)
		)

		if exit_code != 0:
			return errors.throw(errors.XEN_COULDNT_REMOVE)

		return errors.throw(errors.ERR_SUCCESS)

	def modify(self):
		ret = super(xen_core, self).modify()
		if ret: return ret

		# make sure they aren't trying to do something stupid
		if self.disk_space < self.old_data['disk_space']:
			return errors.throw(errors.XEN_CANT_SHRINK_DISK)

		# get relevant paths
		config_path = self.__get_config_path()
		disk_img_path = self.__get_disk_img_path()
		swap_img_path = self.__get_swap_img_path()

		# hostname
		self.new_hostname = self.hostname
		self.set_hostname()

		# memory
		memory = self.g_mem / 1024
		(exit_code,_,_) = self.do_execute(
			"sed" + \
			" -r \"s/^memory = \\\"[0-9]+\\\"$/memory = \\\"%d\\\"/g\"" % (
				memory
			) + " -i \"%s\"" % config_path
		)

		if exit_code != 0:
			return errors.throw(errors.XEN_MODIFY_CONFIG)

		# disk space was changed, so resize the disk image
		if self.disk_space != self.old_data['disk_space']:

			# expand disk image with nulls
			(exit_code,_,_) = self.do_execute(
				"dd if=/dev/zero of=\"%s\" bs=%d count=0 seek=1024" % (
					disk_img_path, self.disk_space
				)
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_DISK_COULDNT_RESIZE)

			# force fsck
			(exit_code,_,_) = self.do_execute(
				"e2fsck -fy \"%s\"" % disk_img_path
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_DISK_COULDNT_FSCK)

			# auto-resize the filesystem
			(exit_code,_,_) = self.do_execute(
				"resize2fs \"%s\"" % disk_img_path
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_DISK_COULDNT_RESIZE)

		# swap space was changed, so resize the swap image
		if self.swap_space != self.old_data['swap_space']:
			# delete old swap file
			(exit_code,_,_) = self.do_execute(
				"rm -f \"%s\"" % swap_img_path
			)

			# create new swap file
			(exit_code,_,_) = self.do_execute(
				"dd if=/dev/zero of=\"%s\" bs=%d count=0 seek=1024" % (
					swap_img_path, self.swap_space
				)
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_DISK_COULDNT_RESIZE)

			# re-create swap
			(exit_code,_,_) = self.do_execute(
				"mkswap \"%s\"" % swap_img_path
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_SWAP_CREATION)

		return errors.throw(errors.ERR_SUCCESS)

	def ost_create(self):
		ret = super(xen_core, self).ost_create()
		if ret: return ret

		# stop VM
		error_code = self.stop()

		if error_code != errors.ERR_SUCCESS:
			return error_code

		# disk path
		disk_img_path = self.__get_disk_img_path()

		# mount loopback file
		loop_dir = self.__mount_loopback(disk_img_path)

		if not loop_dir:
			self.__quit_clean()
			return errors.throw(errors.XEN_MOUNT_LOOP)

		# sanitize OST path
		self.do_execute("mkdir -p \"%s\"" % glob.config.get("paths", "ost_xen"))

		# get OST path and private directories
		ost_path = self.__get_ost_path(self.ost_file)

		# sanity checks
		self.do_execute(
			"rm -f \"%s\" \"%s\"" % (ost_path + ".tar", ost_path + ".tar.gz")
		)

		# get list of directories, then tar it
		(exit_code,_,_) = self.do_execute(
			"ls -1 \"%s\" | xargs tar cf \"%s\" -C \"%s\"" % (
				loop_dir, ost_path + ".tar", loop_dir
			)
		)

		if exit_code != 0:
			self.__quit_clean(loop_dir)
			return errors.throw(errors.ERR_TAR_FAILED)

		# gzip tarred VM
		(exit_code,_,_) = self.do_execute(
			"/usr/bin/env gzip \"%s\"" % (ost_path + ".tar")
		)

		if exit_code != 0:
			self.__quit_clean(loop_dir)
			return errors.throw(errors.ERR_GZIP_FAILED)

		# transfer OST over to master node
		(exit_code,_,_) = execute(
			"scp -i \"%s\" -P %d \"root@%s:%s\" \"%s\"" % (
				glob.config.get("paths", "master_private_key"),
				self.server['port'],
				executer.escape(self.server['ip']),
				ost_path + ".tar.gz", ost_path + ".tar.gz"
			)
		)

		if exit_code != 0:
			self.__quit_clean(loop_dir)
			return errors.throw(errors.ERR_SCP_FAILED)

		# update DB
		(php_exit_code,_,_) = php.db_update(
			"ost", "insert",
			self.ost_name,
			ost_path + ".tar.gz",
			self.ost_driver_id, self.ost_arch
		)

		if php_exit_code != 0:
			self.__quit_clean(loop_dir)
			return php_exit_codes.translate(php_exit_code)

		# clean up
		self.__quit_clean(loop_dir)

		# start it back up
		exit_status = self.start()

		# ignore start errors
		return errors.throw(errors.ERR_SUCCESS)

	# TODO: test
	def rebuild(self):
		ret = super(xen_core, self).rebuild()
		if ret: return ret

		# get relevant paths
		config_path = self.__get_config_path()
		vm_directory = self.__get_vm_directory()

		# get temporary paths
		tmp_vm_directory = vm_directory + ".tmp"

		# sanity
		self.do_execute("rm -rf \"%s\"" % tmp_vm_directory)

		# make temporary directory
		self.do_execute("mkdir -p \"%s\"" % tmp_vm_directory)

		# backup config file
		self.do_execute("mv \"%s\" \"%s\"" % (
			config_path, os.path.join(
				tmp_vm_directory, "%s.cfg" % self.hostname
			)
		))

		# call destroy
		error_code = self.destroy()
		if error_code != errors.ERR_SUCCESS:
			return error_code

		# restore config
		self.do_execute("mv \"%s\" \"%s\"" % (tmp_vm_directory, vm_directory))

		# call create
		error_code = self.create()
		if error_code != errors.ERR_SUCCESS:
			return error_code

		return error_code

	def suspend_ip(self):
		ret = super(xen_core, self).suspend_ip()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	def unsuspend_ip(self):
		ret = super(xen_core, self).unsuspend_ip()
		if ret: return ret
		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# control {{{

	def poweroff(self):
		ret = super(xen_core, self).poweroff()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s shutdown %d -w" % (
				glob.config.get("paths", "xm"),
				self.real_id
			)
		)

		# ignore "Domain doesn't exist" error for shutdown
		ret = xen_exit_codes.translate(exit_code, "shutdown")
		if ret == errors.XEN_DOMAIN_DOESNT_EXIST:
			return errors.throw(errors.ERR_SUCCESS)

		return ret

	def reboot(self):
		ret = super(xen_core, self).reboot()
		if ret: return ret

		(exit_code,_,_) = self.do_execute(
			"%s reboot %d" % (
				glob.config.get("paths", "xm"),
				self.real_id
			)
		)

		return xen_exit_codes.translate(exit_code)

	def start(self):
		ret = super(xen_core, self).start()
		if ret: return ret

		# see if it is started
		(exit_code,stdout,_) = self.do_execute(
			"%s list | /usr/bin/env grep -E \"^%s \"" % (
				glob.config.get("paths", "xm"),
				self.real_id
			)
		)

		# if it is, then nothing to do!
		if len(stdout) != 0:
			return errors.throw(errors.ERR_SUCCESS)

		# get relevant paths
		config_path = self.__get_config_path()
		disk_img_path = self.__get_disk_img_path()

		# grab total RAM being used by existing VMs
		(exit_code,vms,_) = self.do_execute(
			"%s -b -i 1 | /usr/bin/env grep -E \"^ \" | " %
				glob.config.get("paths", "xentop") +
			"/usr/bin/env sed -r \"s/[ ]+/ /g\" | " +
			"/usr/bin/env grep -vE \"^ NAME\" | " +
			"/usr/bin/env grep -vE \"^ Domain-0\" | " +
			"/usr/bin/env grep -vE \"^ %d \" | " % self.real_id +
			"/usr/bin/env cut -d ' ' -f8"
		)

		if exit_code != 0:
			return errors.throw(errors.XEN_RAM_CHECK_FAIL)

		# calculate total RAM
		total_used_ram = 0
		for vm in vms:
			total_used_ram += int(vm)

		# calculate free memory
		(exit_code,total_ram,_) = self.do_execute(
			"/usr/bin/env free | /usr/bin/env grep Mem | " +
			"/usr/bin/env sed -r \"s/[ ]+/ /g\" | " +
			"/usr/bin/env cut -d ' ' -f2"
		)

		# convert to integer
		total_ram = int(total_ram[0])

		if glob.config.get("xen", "use_swap") == "true":
			# calculate free swap
			(exit_code,total_swap,_) = self.do_execute(
				"/usr/bin/env free | /usr/bin/env grep Swap | " +
				"/usr/bin/env sed -r \"s/[ ]+/ /g\" | " +
				"/usr/bin/env cut -d ' ' -f2"
			)

			# add to total
			total_ram += int(total_swap[0])

		# calculate memory to use
		(exit_code,memory,_) = self.do_execute(
			"grep -E \"^memory\" \"%s\" | " % config_path +
			"sed -r \"s/^.*=[ ]*[\\\"]?([0-9]+)[\\\"]?[ ]*$/\\1/g\""
		)

		if exit_code != 0:
			return errors.throw(errors.XEN_RAM_CHECK_FAIL)

		# if RAM exceeds free, then fail
		if total_used_ram + int(memory[0])*1024 > total_ram:
			return errors.throw(errors.XEN_OUT_OF_RAM)

		# sanity
		exit_code = self.__umount_sanity(disk_img_path)

		# something screwy happened to the server
		if exit_code == 0:
			return errors.throw(errors.XEN_SANITY_UMOUNT_FAIL)

		# start the image
		(exit_code,_,_) = self.do_execute(
			"%s create \"%s\"" % (
				glob.config.get("paths", "xm"),
				config_path
			)
		)

		return xen_exit_codes.translate(exit_code, "create")

	def stop(self):
		ret = super(xen_core, self).stop()
		if ret: return ret

		# don't really want destroy here, needs to wait for it to end
		(exit_code,_,_) = self.do_execute(
			"%s shutdown %d -w" % (
				glob.config.get("paths", "xm"),
				self.real_id
			)
		)

		# ignore "Domain doesn't exist" error for shutdown
		ret = xen_exit_codes.translate(exit_code, "shutdown")
		if ret == errors.XEN_DOMAIN_DOESNT_EXIST:
			return errors.throw(errors.ERR_SUCCESS)

		return xen_exit_codes.translate(exit_code, "shutdown")

	# }}}

	# maintenance {{{

	def passwd(self):
		ret = super(xen_core, self).passwd()
		if ret: return ret

		if self.os_type == "LINUX":

			# disk path
			disk_img_path = os.path.join(
				glob.config.get("xen", "root_domain_dir"), str(self.real_id),
				"sda1.img"
			)

			# create loop directory mount point
			loop_dir = tempfile.mkdtemp(prefix = "panenthe")
			self.do_execute("mkdir -p \"%s\"" % loop_dir)

			# mount loopback file
			self.mounts.append(loop_dir)
			(exit_code,_,_) = self.do_execute("mount -o loop \"%s\" \"%s\"" % (
				disk_img_path, loop_dir
			))

			if exit_code != 0:
				return errors.throw(errors.XEN_DISK_READ)

			# run passwd in a chroot
			(exit_code,_,_) = self.do_execute(
				"chroot \"%s\" /bin/bash -c \"echo \\\"%s:%s\\\" | chpasswd\"" %
				(
					loop_dir,
					executer.escape(executer.escape(self.username)),
					executer.escape(executer.escape(self.password))
				)
			)

			# clean up
			self.__quit_clean()

		return errors.throw(errors.ERR_SUCCESS)

	def set_hostname(self):
		ret = super(xen_core, self).set_hostname()
		if ret: return ret

		if self.hostname != self.new_hostname:

			# get relevant paths
			new_config_path = self.__get_config_path(self.new_hostname)
			old_config_path = self.__get_config_path()

			# try to overwrite hostname
			(exit_code,_,_) = self.do_execute(
				"sed -r" + \
					" \"s/^hostname = \\\".*\\\"$/hostname = \\\"%s\\\"/g\"" % (
						self.new_hostname
					) + \
					" -i \"%s\"" % old_config_path
			)

			if exit_code != 0:
				return errors.throw(errors.XEN_MODIFY_CONFIG)

			# now move it to the new place
			(exit_code,_,_) = self.do_execute("mv \"%s\" \"%s\"" % (
				old_config_path, new_config_path
			))

			if exit_code != 0:
				return errors.throw(errors.XEN_MOVE_CONFIG)

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# networking {{{

	def add_ip(self):
		ret = super(xen_core, self).add_ip()
		if ret: return ret

		disk_img_path = self.__get_disk_img_path()

		# linux config
		if self.os_type == "LINUX":

			# mount loopback device
			loop_dir = self.__mount_loopback(disk_img_path)

			if not loop_dir:
				self.__quit_clean()
				return errors.throw(errors.XEN_MOUNT_LOOP)

			# get distro
			self.get_distro(loop_dir)

			# Debian
			if self.distro == "debian":
				# find a non-existing config file
				# SEE FOOTNOTE 1
				(exit_code,stdout,_) = self.do_execute(
					"grep -E \"^auto eth\" \"%s\"" % os.path.join(
						loop_dir, glob.config.get("paths", "network_debian")[1:]
					)
				)

				# just use default
				if exit_code != 0:
					device = "eth0"

				else:
					devices = []
					device = "eth0"
					for line in stdout:
						devices.append(line.split(" ")[1].strip())

					for i in xrange(1, len(devices)+2):
						if devices.count("eth0:%d" % i) == 0:
							device = "eth0:%d" % i
							break

				# populate the config
				# SEE FOOTNOTE 1
				(exit_code,_,_) = self.do_execute("echo -e \"" + \
					"auto %s\\\\n" % device + \
					"iface %s inet static\\\\n" % device + \
					"address %s\\\\n" % self.ip + \
					"netmask %s\\\\n" % self.netmask + \
					"gateway %s\\\\n" % self.gateway + \
					"\" >> \"%s\"" % os.path.join(
						loop_dir, glob.config.get("paths", "network_debian")[1:]
					)
				)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_ADD_IP)

			# Redhat
			elif self.distro == "redhat":
				# find a non-existing config file
				# SEE FOOTNOTE 1
				(exit_code,devices,_) = self.do_execute(
					"ls -1 \"%s\" | grep -E \"^ifcfg-eth\"" % os.path.join(
						loop_dir,
						glob.config.get("paths", "network_redhat_dir")[1:]
					)
				)

				# SEE FOOTNOTE 1
				device_path = os.path.join(
					loop_dir,
					glob.config.get("paths", "network_redhat_dir")[1:], "ifcfg-"
				)

				# if it fails, just assume default
				if exit_code != 0:
					device = "eth0"

				else:
					device = False

					# loop potential devices
					for i in xrange(1, len(devices)+2):
						(exit_code,_,_) = self.do_execute(
							"test -e \"%s%d\"" % (device_path + "eth0:", i)
						)

						if exit_code != 0:
							device = "eth0:%d" % i
							break

					# default device
					if device == False:
						device = "eth0"

				# get device config
				device_config = device_path + device

				# device found, now let's populate the config
				(exit_code,_,_) = self.do_execute("echo -e \"" + \
					"DEVICE=%s\\\\n" % device + \
					"BOOTPROTO=none\\\\n" + \
					"ONBOOT=yes\\\\n" + \
					"NETMASK=%s\\\\n" % self.netmask + \
					"IPADDR=%s\\\\n" % self.ip + \
					"GATEWAY=%s\\\\n" % self.gateway + \
					"TYPE=Ethernet\\\\n" + \
					"\" > \"%s\"" % device_config
				)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_ADD_IP)

			else:
				self.__quit_clean(loop_dir)
				return errors.throw(errors.VPS_UNKNOWN_DISTRO)

			self.__quit_clean()

		return errors.throw(errors.ERR_SUCCESS)

	# TODO: test
	def remove_all_ips(self):
		return errors.ERR_SUCCESS
		ret = super(xen_core, self).remove_all_ips()
		if ret: return ret

		disk_img_path = self.__get_disk_img_path()

		# linux config
		if self.os_type == "LINUX":

			# mount loopback device
			loop_dir = self.__mount_loopback(disk_img_path)

			if not loop_dir:
				self.__quit_clean()
				return errors.throw(errors.XEN_MOUNT_LOOP)

			# get distro
			self.get_distro(loop_dir)

			# Debian
			if self.distro == "debian":
				self.__debian_net_reset(loop_dir)

			# Redhat
			elif self.distro == "redhat":
				# SEE FOOTNOTE 1
				self.do_execute("rm -f %s/ifcfg-eth*" %
					loop_dir,
					glob.config.get("paths", "network_redhat_dir")[1:],
				)

			else:
				self.__quit_clean(loop_dir)
				return errors.throw(errors.VPS_UNKNOWN_DISTRO)

			self.__quit_clean()

		return errors.throw(errors.ERR_SUCCESS)

	def remove_ip(self):
		ret = super(xen_core, self).remove_ip()
		if ret: return ret

		disk_img_path = self.__get_disk_img_path()

		# linux config
		if self.os_type == "LINUX":

			# mount loopback device
			loop_dir = self.__mount_loopback(disk_img_path)

			if not loop_dir:
				self.__quit_clean()
				return errors.throw(errors.XEN_MOUNT_LOOP)

			self.get_distro(loop_dir)

			# Debian
			if self.distro == "debian":
				# the sed is mightier than the awk

				# get ethernet device for the given IP address
				# SEE FOOTNOTE 1
				(exit_code,stdout,_) = self.do_execute(
					"cat \"%s\" | " % os.path.join(
						loop_dir,
						glob.config.get("paths", "network_debian")[1:]
					) + \
					"sed -r \"/^auto eth/b; /^address %s$/b; d\" | " %
						self.ip + \
					"grep -B 1 \"^address %s$\" | grep -m 1 \"\"" % self.ip
				)

				#return errors.XEN_ADD_IP
				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_REMOVE_IP)

				# IP not found, all's good
				out = stdout[0].strip()
				if out == "":
					self.__quit_clean(loop_dir)
					return errors.throw(errors.ERR_SUCCESS)

				# device name
				device = out.split(" ")[1]

				# remove the interface completely from the config
				# SEE FOOTNOTE 1
				(exit_code,_,_) = self.do_execute(
					"sed -r \"" + \
						"/^auto %s$/,/^auto / { " % device + \
						"/^auto %s$/d; /^auto /b; d;" % device + \
					"}\" -i \"%s\"" % os.path.join(
						loop_dir,
						glob.config.get("paths", "network_debian")[1:]
					)
				)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_REMOVE_IP)

				# check if that config file was eth0
				if device == "eth0":
					# if it was eth0, rename the last alias to be the new eth0
					# SEE FOOTNOTE 1
					(exit_code,stdout,_) = self.do_execute(
						"grep -hE \"^auto eth\" \"%s\" | sort" % 
						os.path.join(
							loop_dir,
							glob.config.get("paths", "network_debian")[1:]
						)
					)

					if exit_code != 0:
						self.__quit_clean(loop_dir)
						return errors.throw(errors.XEN_REMOVE_IP)

					# nothing here means no devices present
					if len(stdout) == 0:
						self.__quit_clean(loop_dir)
						return errors.throw(errors.ERR_SUCCESS)

					# get last device
					last_device = stdout[len(stdout)-1].strip()[5:]

					# do replacement
					(exit_code,_,_) = self.do_execute(
						"sed -r \"s/%s/eth0/g\" -i \"%s\"" % (
							last_device,
							os.path.join(
								loop_dir,
								glob.config.get("paths", "network_debian")[1:]
							)
						)
					)

					if exit_code != 0:
						self.__quit_clean(loop_dir)
						return errors.throw(errors.XEN_REMOVE_IP)

			# Redhat
			elif self.distro == "redhat":
				# get filename
				# SEE FOOTNOTE 1
				(exit_code,stdout,_) = self.do_execute(
					"grep -cHE \"^IPADDR=%s$\" \"%s/ifcfg-eth\"*" % (
						self.ip,
						os.path.join(
							loop_dir,
							glob.config.get("paths", "network_redhat_dir")[1:]
						)
					)
				)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					# IP not found, so we at least did our job
					return errors.throw(errors.ERR_SUCCESS)

				# output format is this:
				#   filename:#
				# however, we are dealing with network aliases, which means
				# "filename" may contain a second semicolon.  We must strip off
				# the last semicolon and number which represent the count, and
				# we have to filter the results for the counte to be greater
				# than 0
				device_file = None
				for line in stdout:
					count = int(line[line.rfind(":")+1:])
					if int(count) > 0:
						device_file = line[:line.rfind(":")]
						break

				# IP not found
				if device_file == None:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_IP_NOT_FOUND)

				# delete config file
				(exit_code,_,_) = self.do_execute("rm -f \"%s\"" % device_file)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_REMOVE_IP)

				# check if that config file was eth0
				if device_file[-4:] == "eth0":
					# if it was eth0, rename the last alias to be the new eth0
					# SEE FOOTNOTE 1
					(exit_code,stdout,_) = self.do_execute(
						"ls -1 --indicator-style=none \"%s\"*" %
						os.path.join(
							loop_dir,
							glob.config.get("paths", "network_redhat_dir")[1:],
							"ifcfg-eth"
						)
					)

					if exit_code != 0:
						self.__quit_clean(loop_dir)
						return errors.throw(errors.XEN_REMOVE_IP)

					if len(stdout) == 0:
						self.__quit_clean(loop_dir)
						# device not found, so we at least did our job
						return errors.throw(errors.ERR_SUCCESS)

					# figure out the last device
					last_dev_config = stdout[len(stdout)-1].strip()

					# overwrite device label inside the config
					(exit_code,_,_) = self.do_execute(
						"sed -r \"s/^DEVICE=.*$/DEVICE=eth0/g\" -i \"%s\"" %
							last_dev_config
					)

					if exit_code != 0:
						self.__quit_clean(loop_dir)
						return errors.throw(errors.XEN_REMOVE_IP)

					# move the old device to eth0
					(exit_code,_,_) = self.do_execute(
						"mv \"%s\" \"%s\"" % (
							last_dev_config, device_file
						)
					)

					if exit_code != 0:
						self.__quit_clean(loop_dir)
						return errors.throw(errors.XEN_REMOVE_IP)

			# what distro are you using? we don't support you
			else:
				self.__quit_clean(loop_dir)
				return errors.throw(errors.VPS_UNKNOWN_DISTRO)

			self.__quit_clean()

		return errors.throw(errors.ERR_SUCCESS)

	def set_dns(self):
		ret = super(xen_core, self).set_dns()
		if ret: return ret

		disk_img_path = self.__get_disk_img_path()

		# linux config
		if self.os_type == "LINUX":

			# mount loopback device
			loop_dir = self.__mount_loopback(disk_img_path)

			if not loop_dir:
				self.__quit_clean()
				return errors.throw(errors.XEN_MOUNT_LOOP)

			# clear the file
			# SEE FOOTNOTE 1
			(exit_code,_,_) = self.do_execute("echo > \"%s\"" % os.path.join(
				loop_dir, glob.config.get("paths", "dns_file")[1:]
			))

			if exit_code != 0:
				self.__quit_clean(loop_dir)
				return errors.throw(errors.XEN_SET_DNS)

			# add names to resolv.conf
			dnss = self.dns.split(" ")
			for dns in dnss:
				# SEE FOOTNOTE 1
				(exit_code,_,_) = self.do_execute(
					"echo \"nameserver %s\" >> \"%s\"" % (
						dns, os.path.join(
							loop_dir, glob.config.get("paths", "dns_file")[1:]
						)
					)
				)

				if exit_code != 0:
					self.__quit_clean(loop_dir)
					return errors.throw(errors.XEN_SET_DNS)

			self.__quit_clean()

		return errors.throw(errors.ERR_SUCCESS)

	# }}}

	# status {{{

	def load_averages(self):
		ret = super(xen_core, self).load_averages()
		if ret: return ret
		return errors.BACKEND_NOT_IMPLEMENTED

	def status(self):
		ret = super(xen_core, self).status()
		if ret: return ret

		self.__get_xentop_info()

		# if we didn't find it, it's not started
		if self.xentop_info == -1:
			return errors.throw(errors.VPS_STATUS_STOPPED)

		return errors.throw(errors.VPS_STATUS_RUNNING)

	# does not use the default threads that other drivers use
	def status_update_all(self):
		# get xentop info
		self.__get_xentop_info()

		# if we didn't find it, it's not started
		if self.xentop_info == -1:
			return errors.throw(errors.VPS_STATUS_STOPPED)

		# Xen specific threads
		threads = [
			generic_thread(self.uptime),
			generic_thread(self.usage_bandwidth),
			generic_thread(self.usage_disk),
			generic_thread(self.__usage_cpu),
			generic_thread(self.__usage_mem)
		]

		# call super
		ret = super(xen_core, self).status_update_all(replace_status = threads)
		if ret: return ret
		return errors.throw(errors.XEN_ERR_UNKNOWN)

	def uptime(self):
		ret = super(xen_core, self).uptime()
		if ret: return ret

		# get uptime
		(exit_code,stdout,_) = self.do_execute(
			"%s uptime %d" % (
				glob.config.get("paths", "xm"),
				self.real_id
			)
		)

		if exit_code != 0:
			return xen_exit_codes.translate(exit_code)

		# parse uptime
		uptime_split = re.sub("[ ]+", " ", stdout[1].strip()).split(" ")
		uptime_split = uptime_split[2:]
		uptime = " ".join(uptime_split)

		# update db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"uptime", str(uptime)
		)

		return php_exit_codes.translate(php_exit_code)

	def usage_bandwidth(self):
		ret = super(xen_core, self).usage_bandwidth()
		if ret: return ret
		return errors.throw(errors.XEN_ERR_UNKNOWN)

	def __usage_cpu(self):
		# update db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_cpu_system", str(self.xentop_info[3]) # 3rd column is CPU
		)

		return php_exit_codes.translate(php_exit_code)

	def usage_cpu(self):
		ret = super(xen_core, self).usage_cpu()
		if ret: return ret
		self.__get_xentop_info()
		return self.__usage_cpu()

	def usage_disk(self):
		ret = super(xen_core, self).usage_disk()
		if ret: return ret

		(exit_code,stdout,_) = self.do_execute("du \"%s\"" % os.path.join(
			glob.config.get("xen", "root_domain_dir"),
			str(self.real_id), "sda1.img"
		))

		# calculate space
		used_space = stdout[0].split("\t")[0]

		# update db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_disk", used_space # 3rd column is CPU
		)

		return php_exit_codes.translate(php_exit_code)

	def __usage_mem(self):
		# update db
		(php_exit_code,_,_) = php.db_update(
			"vps_stats", "update_attribute",
			str(self.server['server_id']), str(self.vps_id),
			"usage_mem", str(self.xentop_info[4]) # 4th column is memory
		)

		return php_exit_codes.translate(php_exit_code)

	def usage_mem(self):
		ret = super(xen_core, self).usage_mem()
		if ret: return ret
		self.__get_xentop_info()
		return self.__usage_mem()

	def users(self):
		ret = super(xen_core, self).users()
		if ret: return ret
		return errors.BACKEND_NOT_IMPLEMENTED

	# }}}

	# internal VPS commands {{{

	def service_restart(self):
		ret = super(xen_core, self).service_restart()
		if ret: return ret
		return errors.BACKEND_NOT_IMPLEMENTED

	def service_start(self):
		ret = super(xen_core, self).service_start()
		if ret: return ret
		return errors.BACKEND_NOT_IMPLEMENTED

	def service_stop(self):
		ret = super(xen_core, self).service_stop()
		if ret: return ret
		return errors.BACKEND_NOT_IMPLEMENTED

	# }}}

	# XEN specific {{{

	# }}}

#
# FOOTNOTES:
# 1 - "[1:]" takes off the first slash in the absolute path os.path.join() will
#     convert ("/tmp", "/etc") to "/etc", so we have to do ("/tmp", "etc") which
#     will give us the "/tmp/etc" that we desire
#
