ALTER TABLE `vps` CHANGE `disk_space` `disk_space` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `backup_space` `backup_space` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `swap_space` `swap_space` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `g_mem` `g_mem` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `b_mem` `b_mem` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `cpu_num` `cpu_num` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `in_bw` `in_bw` VARCHAR( 128 ) NOT NULL DEFAULT '0',
CHANGE `out_bw` `out_bw` VARCHAR( 128 ) NOT NULL DEFAULT '0';
