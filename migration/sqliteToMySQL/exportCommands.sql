.mode insert rubrik
.out export/rubrik.sql 
select id, rubrik, reihenfolge from rubrik;

/* Paramter 9 to the id of lieder@adoray.ch user !!!!!!!!!!!!!!!!!!!!!!*/
.mode insert lied
.out export/lied.sql 
select id, titel, rubrik_id, stichwoerter, bemerkungen, created_at, updated_at, NULL, 8, NULL from Lied;

.mode insert liederbuch
.out export/liederbuch.sql 
select id, buchname, beschreibung, id, NULL from liederbuch;

.mode insert fkliederbuchlied
.out export/fkliederbuchlied.sql 
select id, liederbuch_id, lied_id, Liednr from fkliederbuchlied;

.mode insert refrain
.out export/refrain.sql 
/* RefrainNr is changed to Reihenfolge!!!!!!!!!!!!!!!! */
select id, lied_id, RefrainNr, Refrain, NULL from refrain;

.mode insert liedtext
.out export/liedtext.sql 
select id, lied_id, Ueberschrift, UeberschriftTyp, Strophe, refrain_id, Reihenfolge, NULL from liedtext;

.mode insert settings
.out export/settings.sql 
/*We don't want the old schema version to be exported but all other settings and the new one.*/
select key, value from settings WHERE key != 'database.schema.version';
/*
CHANGE ME!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
This is the version of the schema to which the following insert statements will fit.
If you update the new database schema you need to adjust this export script and the followin insert statements accordingly.
*/
select 'database.schema.version', '100011';

.quit;