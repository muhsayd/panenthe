INSERT INTO `panenthe`.`drivers` (
`driver_id` ,
`ext_ref` ,
`name` ,
`version`
)
VALUES (
NULL , 'xen', 'Xen', '1,0,0'
);

ALTER TABLE `servers` ADD `driver_id` INT( 20 ) NULL AFTER `parent_server_id` ,
ADD INDEX ( `driver_id` );

ALTER TABLE `vps` ADD `kernel` VARCHAR( 255 ) NULL AFTER `real_id` ;

ALTER TABLE `ip_map` DROP INDEX `ip_addr`;
ALTER TABLE `ip_map` ADD INDEX ( `ip_addr` );
ALTER TABLE `vps_status_history` DROP INDEX `message`;
ALTER TABLE `ip_map` ADD INDEX ( `vps_id` ) ;
ALTER TABLE `ip_pools_map` ADD INDEX ( `server_id` ) ;
ALTER TABLE `vps` ADD INDEX ( `server_id` ) ;

ALTER TABLE `users` CHANGE `email` `email` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `first_name` `first_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `last_name` `last_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `created` `created` BIGINT( 40 ) NULL ,
CHANGE `modified` `modified` BIGINT( 40 ) NULL ,
CHANGE `last_login` `last_login` BIGINT( 40 ) NULL ,
CHANGE `last_refresh` `last_refresh` BIGINT( 40 ) NULL


ALTER TABLE `data`  ENGINE = InnoDB;
ALTER TABLE `drivers`  ENGINE = InnoDB;
ALTER TABLE `events`  ENGINE = InnoDB;
ALTER TABLE `ip_map`  ENGINE = InnoDB;
ALTER TABLE `ip_pools`  ENGINE = InnoDB;
ALTER TABLE `ip_pools_map`  ENGINE = InnoDB;
ALTER TABLE `ost`  ENGINE = InnoDB;
ALTER TABLE `plans`  ENGINE = InnoDB;
ALTER TABLE `servers`  ENGINE = InnoDB;
ALTER TABLE `server_stats`  ENGINE = InnoDB;
ALTER TABLE `users`  ENGINE = InnoDB;
ALTER TABLE `vps`  ENGINE = InnoDB;
ALTER TABLE `vps_stats`  ENGINE = InnoDB;
ALTER TABLE `vps_status_history`  ENGINE = InnoDB;
ALTER TABLE `vps_user_map`  ENGINE = InnoDB;

ALTER TABLE `ip_map` ADD FOREIGN KEY ( `ip_pool_id` ) REFERENCES `panenthe`.`ip_pools` (
`ip_pool_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `ip_map` ADD FOREIGN KEY ( `vps_id` ) REFERENCES `panenthe`.`vps` (
`vps_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `ip_pools_map` ADD FOREIGN KEY ( `ip_pool_id` ) REFERENCES `panenthe`.`ip_pools` (
`ip_pool_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `ip_pools_map` ADD FOREIGN KEY ( `server_id` ) REFERENCES `panenthe`.`servers` (
`server_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `ost` ADD FOREIGN KEY ( `driver_id` ) REFERENCES `panenthe`.`drivers` (
`driver_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `servers` ADD FOREIGN KEY ( `driver_id` ) REFERENCES `panenthe`.`drivers` (
`driver_id`
) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE `server_stats` ADD FOREIGN KEY ( `server_id` ) REFERENCES `panenthe`.`servers` (
`server_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vps` ADD FOREIGN KEY ( `server_id` ) REFERENCES `panenthe`.`servers` (
`server_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vps` ADD FOREIGN KEY ( `driver_id` ) REFERENCES `panenthe`.`drivers` (
`driver_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vps_stats` ADD FOREIGN KEY ( `vps_id` ) REFERENCES `panenthe`.`vps` (
`vps_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vps_status_history` ADD FOREIGN KEY ( `vps_id` ) REFERENCES `panenthe`.`vps` (
`vps_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `vps_user_map` ADD FOREIGN KEY ( `user_id` ) REFERENCES `panenthe`.`users` (
`user_id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

