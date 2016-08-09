ALTER TABLE filemetadata 
ADD CONSTRAINT `fkFilemetadataLied`
  FOREIGN KEY (`lied_id`)
  REFERENCES `dockerdefault`.`lied` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;