ALTER TABLE `lied` 
DROP FOREIGN KEY `liedLastEditUser`;
ALTER TABLE `lied` 
CHANGE COLUMN `Titel` `Titel` TEXT NOT NULL ,
CHANGE COLUMN `created_at` `created_at` TIMESTAMP DEFAULT 0, /* Will set to 0 --> Insert Trigger!*/
CHANGE COLUMN `updated_at` `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CHANGE COLUMN `lastEditUser_id` `lastEditUser_id` BIGINT(20) NOT NULL ;
ALTER TABLE `lied` 
ADD CONSTRAINT `liedLastEditUser`
  FOREIGN KEY (`lastEditUser_id`)
  REFERENCES `user` (`id`)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;