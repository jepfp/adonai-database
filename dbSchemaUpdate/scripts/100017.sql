#For now, we only allow one file per Lied
ALTER TABLE `scottyinttest`.`file` 
ADD UNIQUE INDEX `lied_id_UNIQUE` (`lied_id` ASC);