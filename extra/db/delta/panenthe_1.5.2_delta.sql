ALTER TABLE `vps_status_history` DROP INDEX `message`;

ALTER TABLE `data` ENGINE=InnoDB;
ALTER TABLE `drivers` ENGINE=InnoDB;
ALTER TABLE `events` ENGINE=InnoDB;
ALTER TABLE `ip_map` ENGINE=InnoDB;
ALTER TABLE `ip_pools` ENGINE=InnoDB;
ALTER TABLE `ip_pools_map` ENGINE=InnoDB;
ALTER TABLE `ost` ENGINE=InnoDB;
ALTER TABLE `plans` ENGINE=InnoDB;
ALTER TABLE `servers` ENGINE=InnoDB;
ALTER TABLE `server_stats` ENGINE=InnoDB;
ALTER TABLE `users` ENGINE=InnoDB;
ALTER TABLE `vps` ENGINE=InnoDB;
ALTER TABLE `vps_stats` ENGINE=InnoDB;
ALTER TABLE `vps_status_history` ENGINE=InnoDB;
ALTER TABLE `vps_user_map` ENGINE=InnoDB;

ALTER TABLE `servers` ADD COLUMN `driver_id` INT(20) DEFAULT NULL AFTER `parent_server_id`;
ALTER TABLE `ost` ADD COLUMN `os_type` VARCHAR(255) NOT NULL DEFAULT 'LINUX' AFTER `arch`;

ALTER TABLE `ip_map`
  ADD CONSTRAINT `ip_map_ibfk_1` FOREIGN KEY (`ip_pool_id`) REFERENCES `ip_pools` (`ip_pool_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ip_map_ibfk_2` FOREIGN KEY (`vps_id`) REFERENCES `vps` (`vps_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ip_pools_map`
  ADD CONSTRAINT `ip_pools_map_ibfk_1` FOREIGN KEY (`ip_pool_id`) REFERENCES `ip_pools` (`ip_pool_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ip_pools_map_ibfk_2` FOREIGN KEY (`server_id`) REFERENCES `servers` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ost`
  ADD CONSTRAINT `ost_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `servers`
  ADD CONSTRAINT `servers_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `server_stats`
  ADD CONSTRAINT `server_stats_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `servers` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `vps`
  ADD CONSTRAINT `vps_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `servers` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vps_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `vps_stats`
  ADD CONSTRAINT `vps_stats_ibfk_1` FOREIGN KEY (`vps_id`) REFERENCES `vps` (`vps_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `vps_status_history`
  ADD CONSTRAINT `vps_status_history_ibfk_1` FOREIGN KEY (`vps_id`) REFERENCES `vps` (`vps_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `vps_user_map`
  ADD CONSTRAINT `vps_user_map_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `servers` SET `driver_id` = 4;
INSERT INTO `drivers` VALUES (5, 'xen', 'Xen', '1.0.0');
