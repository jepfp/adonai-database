CREATE 
     OR REPLACE ALGORITHM = MERGE 
VIEW `lied_table_view` AS
    select 
        `l`.`id` AS `id`,
        `l`.`Titel` AS `Titel`,
        `r`.`Rubrik` AS `Rubrik`,
        `l`.`tonality` AS `tonality`,
        `l`.`created_at` AS `created_at`,
        `l`.`updated_at` AS `updated_at`,
        `l`.`lastEditUser_id` AS `lastEditUser_id`,
        `u`.`email` AS `email`
    from
        (`lied` `l`
        join `user` `u` ON (`l`.`lastEditUser_id` = `u`.`id`)
        left join `rubrik` `r` ON ((`l`.`rubrik_id` = `r`.`id`)));
        
CREATE 
     OR REPLACE ALGORITHM = MERGE 
VIEW `every_lied_in_every_liederbuch_view` AS
    select 
        `l`.`id` AS `id`,
        `l`.`Titel` AS `Titel`,
        `b`.`id` AS `id_liederbuch`,
        `b`.`Buchname` AS `Buchname`,
        `l`.`Rubrik` AS `Rubrik`,
        `l`.`tonality` AS `tonality`,
        `l`.`created_at` AS `created_at`,
        `l`.`updated_at` AS `updated_at`,
		`l`.`lastEditUser_id` AS `lastEditUser_id`,
        `l`.`email` AS `email`
    from
        (`lied_table_view` `l`
        join `liederbuch` `b`);
        
CREATE 
     OR REPLACE ALGORITHM = MERGE 
VIEW `liedview` AS
    select 
        `l`.`id` AS `id`,
        `ll`.`Liednr` AS `Liednr`,
        `l`.`Titel` AS `Titel`,
        `l`.`id_liederbuch` AS `id_liederbuch`,
        `l`.`Buchname` AS `Buchname`,
        `l`.`Rubrik` AS `Rubrik`,
        `l`.`tonality` AS `tonality`,
        `l`.`created_at` AS `created_at`,
        `l`.`updated_at` AS `updated_at`,
		`l`.`lastEditUser_id` AS `lastEditUser_id`,
        `l`.`email` AS `email`
    from
        (`every_lied_in_every_liederbuch_view` `l`
        left join `fkliederbuchlied` `ll` ON (((`l`.`id` = `ll`.`lied_id`)
            and (`l`.`id_liederbuch` = `ll`.`liederbuch_id`))));
        