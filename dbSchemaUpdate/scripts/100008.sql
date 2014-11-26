begin;
drop trigger if exists onUpdateLiedtextUpdateLied;
drop trigger if exists onInsertLiedtextUpdateLied;
drop trigger if exists onDeleteLiedtextUpdateLied;

drop trigger if exists onUpdateRefrainUpdateLied;
drop trigger if exists onInsertRefrainUpdateLied;
drop trigger if exists onDeleteRefrainUpdateLied;

CREATE TRIGGER onInsertLiedSetCreatedAt
BEFORE INSERT ON lied
FOR EACH ROW
set NEW.created_at = NOW();

commit;