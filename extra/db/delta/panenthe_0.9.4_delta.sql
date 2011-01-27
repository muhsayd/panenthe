DROP TABLE IF EXISTS `data`;
CREATE TABLE IF NOT EXISTS `data` (
  `data_id` int(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `data` (`data_id`, `name`, `value`, `active`) VALUES
(2, 'welcome_email', '<html>\r\n<head>\r\n	<title>VPS Welcome Email</title>\r\n</head>\r\n<body>\r\n\r\nDear <strong>[[first_name]] [[last_name]]</strong>,\r\n<br /><br />\r\n\r\nA new virtual machine with the hostname of [[hostname]] has been created and assigned to you.\r\n<br /><br />\r\n\r\nTo login to manage your VM use the following.<br />\r\nURL: <a href="[[site_url]]">[[site_url]]</a><br />\r\nUsername: <strong>[[username]]</strong><br />\r\nPassword: <strong>[[password]]</strong><br />\r\n<br />\r\n\r\nTo SSH into your VM (provided its linux) use the following:<br />\r\nIP: <strong>[[ip_address]]</strong><br />\r\nPort: <strong>22</strong><br />\r\nUser: <strong>root</strong><br />\r\nPassword: <strong>[[root_password]]</strong><br />\r\n<br />\r\n\r\nOther VM details.<br />\r\nOS Template: <strong>[[ost]]</strong><br />\r\nDisk Space: <strong>[[disk_space]] MB</strong><br />\r\nBackup Space: <strong>[[backup_space]] MB</strong><br />\r\nSwap Space: <strong>[[swap_space]] MB</strong><br />\r\nGuaranteed Memory: <strong>[[g_mem]] MB</strong><br />\r\nBurstable Memory: <strong>[[b_mem]] MB</strong><br />\r\nCPU Percentage: <strong>[[cpu_pct]]</strong><br />\r\nCPU Multiplier: <strong>{cpu_num]]</strong><br />\r\nOutgoing Traffic: <strong>{out_bw} GB</strong><br />\r\nIncoming Traffic: <strong>{in_bw} GB</strong> <small>Note: if this is left blank traffic is tracked full duplex by the outgroing limit.</small><br />\r\n<br />\r\n\r\nThanks.<br />\r\n[[site_name]]\r\n\r\n</body>\r\n</html>', 1);

