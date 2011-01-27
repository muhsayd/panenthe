ALTER TABLE `vps` CHANGE `disk_space` `disk_space` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `backup_space` `backup_space` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `swap_space` `swap_space` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `g_mem` `g_mem` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `b_mem` `b_mem` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `cpu_num` `cpu_num` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `in_bw` `in_bw` INT( 40 ) NOT NULL DEFAULT '0',
CHANGE `out_bw` `out_bw` INT( 40 ) NOT NULL DEFAULT '0';
