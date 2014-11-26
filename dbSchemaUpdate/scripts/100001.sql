CREATE 
    ALGORITHM = MERGE 
VIEW lied_table_view AS
    SELECT 
        l.id,
        l.Titel,
        r.Rubrik,
        l.tonality,
        l.created_at,
        l.updated_at
    FROM
        lied l
            LEFT JOIN
        rubrik r ON (l.rubrik_id = r.id);
		
CREATE 
    ALGORITHM = MERGE 
VIEW every_lied_in_every_liederbuch_view AS
    SELECT 
        l.id as id,
        l.Titel,
        b.id as id_liederbuch,
        b.Buchname,
        l.Rubrik,
        l.tonality,
        l.created_at,
        l.updated_at
    FROM
        lied_table_view l,
        liederbuch b;
		
CREATE 
    ALGORITHM = MERGE 
VIEW liedview AS
    SELECT 
        l.id,
        ll.Liednr,
        l.Titel,
        l.id_liederbuch,
        l.Buchname,
        l.Rubrik,
        l.tonality,
        l.created_at,
        l.updated_at
    FROM
        every_lied_in_every_liederbuch_view l
            LEFT JOIN
        fkliederbuchlied ll ON (l.id = ll.lied_id
            AND l.id_liederbuch = ll.liederbuch_id);
			
/*select * from liedview where id_liederbuch = 1 ORDER BY ISNULL(Liednr), Liednr * 1, Liednr ASC;*/