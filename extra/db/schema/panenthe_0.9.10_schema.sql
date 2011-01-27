-- phpMyAdmin SQL Dump
-- version 2.11.9.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 09, 2009 at 07:43 PM
-- Server version: 5.0.45
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `panenthe`
--

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE IF NOT EXISTS `data` (
  `data_id` int(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`data_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE IF NOT EXISTS `drivers` (
  `driver_id` int(20) NOT NULL auto_increment,
  `ext_ref` varchar(40) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(10) NOT NULL default '1,0,0',
  PRIMARY KEY  (`driver_id`),
  UNIQUE KEY `ext_ref` (`ext_ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(20) NOT NULL auto_increment,
  `message` text NOT NULL,
  `time` bigint(40) NOT NULL,
  `is_acknowledged` tinyint(1) NOT NULL default '0',
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  PRIMARY KEY  (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2306 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_map`
--

CREATE TABLE IF NOT EXISTS `ip_map` (
  `ip_id` int(20) NOT NULL auto_increment,
  `ip_addr` varchar(255) NOT NULL,
  `ip_pool_id` int(20) NOT NULL,
  `vps_id` int(20) NOT NULL,
  PRIMARY KEY  (`ip_id`),
  UNIQUE KEY `ip_addr` (`ip_addr`),
  KEY `ip_pool_id` (`ip_pool_id`,`vps_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=95 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_pools`
--

CREATE TABLE IF NOT EXISTS `ip_pools` (
  `ip_pool_id` int(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `first_ip` varchar(255) NOT NULL,
  `last_ip` varchar(255) NOT NULL,
  `dns` varchar(255) NOT NULL,
  `gateway` varchar(255) NOT NULL,
  `netmask` varchar(255) NOT NULL,
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  PRIMARY KEY  (`ip_pool_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_pools_map`
--

CREATE TABLE IF NOT EXISTS `ip_pools_map` (
  `ip_pool_id` int(20) NOT NULL,
  `server_id` int(20) NOT NULL,
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  KEY `ip_pool_id` (`ip_pool_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ost`
--

CREATE TABLE IF NOT EXISTS `ost` (
  `ost_id` int(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `driver_id` int(20) NOT NULL,
  `arch` varchar(255) NOT NULL,
  PRIMARY KEY  (`ost_id`),
  KEY `driver_id` (`driver_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE IF NOT EXISTS `plans` (
  `plan_id` int(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `disk_space` int(20) NOT NULL default '2000',
  `backup_space` int(20) NOT NULL default '0',
  `swap_space` int(20) NOT NULL default '0',
  `g_mem` int(20) NOT NULL default '512',
  `b_mem` int(20) NOT NULL default '0',
  `cpu_pct` int(3) NOT NULL default '100',
  `cpu_num` int(20) NOT NULL default '1',
  `out_bw` int(20) NOT NULL default '4000',
  `in_bw` int(20) NOT NULL default '2000',
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  PRIMARY KEY  (`plan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `server_id` int(20) NOT NULL auto_increment,
  `parent_server_id` int(20) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `datacenter` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `port` int(6) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  PRIMARY KEY  (`server_id`),
  KEY `parent_server_id` (`parent_server_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `server_stats`
--

CREATE TABLE IF NOT EXISTS `server_stats` (
  `server_stat_id` int(20) NOT NULL auto_increment,
  `server_id` int(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`server_stat_id`),
  KEY `server_id` (`server_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(20) NOT NULL auto_increment,
  `ext_ref` varchar(40) NOT NULL,
  `short_desc` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  PRIMARY KEY  (`setting_id`),
  UNIQUE KEY `ext_ref` (`ext_ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(20) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(40) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `is_staff` tinyint(1) NOT NULL default '0',
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  `last_login` bigint(40) NOT NULL,
  `last_refresh` bigint(40) NOT NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Table structure for table `vps`
--

CREATE TABLE IF NOT EXISTS `vps` (
  `vps_id` int(20) NOT NULL auto_increment,
  `server_id` int(20) NOT NULL,
  `driver_id` int(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `real_id` varchar(255) default NULL,
  `disk_space` int(20) NOT NULL default '2000',
  `backup_space` int(20) NOT NULL default '0',
  `swap_space` int(20) NOT NULL default '0',
  `g_mem` int(20) NOT NULL default '512',
  `b_mem` int(20) NOT NULL default '0',
  `cpu_pct` int(3) NOT NULL default '100',
  `cpu_num` int(20) NOT NULL default '1',
  `in_bw` int(20) NOT NULL default '2000',
  `out_bw` int(20) NOT NULL default '4000',
  `ost` int(20) NOT NULL,
  `is_running` tinyint(1) default '0',
  `is_suspended` tinyint(1) NOT NULL default '0',
  `created` bigint(40) default NULL,
  `modified` bigint(40) default NULL,
  PRIMARY KEY  (`vps_id`),
  KEY `driver_id` (`driver_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=159 ;

-- --------------------------------------------------------

--
-- Table structure for table `vps_stats`
--

CREATE TABLE IF NOT EXISTS `vps_stats` (
  `vps_stat_id` int(20) NOT NULL auto_increment,
  `vps_id` int(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`vps_stat_id`),
  KEY `vps_id` (`vps_id`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `vps_status_history`
--

CREATE TABLE IF NOT EXISTS `vps_status_history` (
  `vps_status_history_id` int(20) NOT NULL auto_increment,
  `vps_id` int(20) NOT NULL,
  `message` text NOT NULL,
  `time` bigint(40) NOT NULL,
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  PRIMARY KEY  (`vps_status_history_id`),
  KEY `vps_id` (`vps_id`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `vps_user_map`
--

CREATE TABLE IF NOT EXISTS `vps_user_map` (
  `vps_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `created` bigint(40) NOT NULL,
  `modified` bigint(40) NOT NULL,
  KEY `vps_id` (`vps_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

