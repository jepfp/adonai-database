DELETE FROM fkliederbuchlied where Liednr is null;

ALTER TABLE `fkliederbuchlied` 
DROP FOREIGN KEY `fkLiederbuchLiedLied`,
DROP FOREIGN KEY `fkLiederbuchLiedLiederbuch`;
ALTER TABLE `fkliederbuchlied` 
CHANGE COLUMN `liederbuch_id` `liederbuch_id` BIGINT(20) NOT NULL ,
CHANGE COLUMN `lied_id` `lied_id` BIGINT(20) NOT NULL ,
CHANGE COLUMN `Liednr` `Liednr` VARCHAR(20) NOT NULL ;
ALTER TABLE `fkliederbuchlied` 
ADD CONSTRAINT `fkLiederbuchLiedLied`
  FOREIGN KEY (`lied_id`)
  REFERENCES `lied` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fkLiederbuchLiedLiederbuch`
  FOREIGN KEY (`liederbuch_id`)
  REFERENCES `liederbuch` (`ID`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
