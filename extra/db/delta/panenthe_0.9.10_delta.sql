 ALTER TABLE `vps_stats` CHANGE `value` `value` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
 
 CREATE TABLE `panenthe`.`server_stats` (
`server_stat_id` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`server_id` INT( 20 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`value` TEXT NOT NULL ,
INDEX ( `server_id` )
) ENGINE = MYISAM ; 

ALTER TABLE `vps` ADD `is_suspended` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `is_running` ;

ALTER TABLE `vps` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `driver_id` ;
