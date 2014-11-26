SET foreign_key_checks = 0;
ALTER TABLE `language` 
CHANGE COLUMN `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `code` `code` VARCHAR(5) NOT NULL ;
SET foreign_key_checks = 1;