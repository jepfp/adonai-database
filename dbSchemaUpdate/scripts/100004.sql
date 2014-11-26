ALTER TABLE `liedtext` 
DROP FOREIGN KEY `liedtextLied`;
ALTER TABLE `liedtext` 
CHANGE COLUMN `lied_id` `lied_id` BIGINT(20) NOT NULL ;
ALTER TABLE `liedtext` 
ADD CONSTRAINT `liedtextLied`
  FOREIGN KEY (`lied_id`)
  REFERENCES `lied` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
