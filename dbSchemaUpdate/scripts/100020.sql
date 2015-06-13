begin;
CREATE TABLE `filemetadata` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `filetype` ENUM('sourcepdf') NOT NULL,
  `lied_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fkLied_idx` (`lied_id` ASC),
  CONSTRAINT 
    FOREIGN KEY (`lied_id`)
    REFERENCES `lied` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);
    
insert into filemetadata (id, lied_id) select id, lied_id from `file`;
ALTER TABLE `file` 
ADD COLUMN `filemetadata_id` BIGINT NULL AFTER `lied_id`,
ADD UNIQUE INDEX `filemetadata_id_UNIQUE` (`filemetadata_id` ASC);

update `file` set filemetadata_id = id;

ALTER TABLE `file` 
CHANGE COLUMN `filemetadata_id` `filemetadata_id` BIGINT(20) NOT NULL ;
ALTER TABLE `file` 
ADD CONSTRAINT `file_ibfk_1`
  FOREIGN KEY (`filemetadata_id`)
  REFERENCES `filemetadata` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
ALTER TABLE `file` DROP FOREIGN KEY `fkLied`;
ALTER TABLE `file` 
DROP COLUMN `lied_id`,
DROP INDEX `fkLied_idx` ,
DROP INDEX `lied_id_UNIQUE` ;
commit;
