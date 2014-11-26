CREATE TRIGGER onUpdateRefrainUpdateLied
BEFORE UPDATE ON refrain
FOR EACH ROW
update lied set updated_at = NOW() where id = NEW.lied_id;

CREATE TRIGGER onInsertRefrainUpdateLied
BEFORE INSERT ON refrain
FOR EACH ROW
update lied set updated_at = NOW() where id = NEW.lied_id;

CREATE TRIGGER onDeleteRefrainUpdateLied
BEFORE DELETE ON refrain
FOR EACH ROW
update lied set updated_at = NOW() where id = OLD.lied_id;

CREATE TRIGGER onUpdateLiedtextUpdateLied
BEFORE UPDATE ON liedtext
FOR EACH ROW
update lied set updated_at = NOW() where id = NEW.lied_id;

CREATE TRIGGER onInsertLiedtextUpdateLied
BEFORE INSERT ON liedtext
FOR EACH ROW
update lied set updated_at = NOW() where id = NEW.lied_id;

CREATE TRIGGER onDeleteLiedtextUpdateLied
BEFORE DELETE ON liedtext
FOR EACH ROW
update lied set updated_at = NOW() where id = OLD.lied_id;