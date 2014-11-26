CREATE TABLE fkLiederbuchLied (id INTEGER PRIMARY KEY AUTOINCREMENT, liederbuch_id INTEGER, lied_id INTEGER, liednr TEXT);
CREATE TABLE Lied (id INTEGER PRIMARY KEY AUTOINCREMENT, titel TEXT, rubrik_id INTEGER, stichwoerter TEXT, bemerkungen TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL);
CREATE TABLE Liederbuch (id INTEGER PRIMARY KEY AUTOINCREMENT, buchname TEXT, beschreibung TEXT);
CREATE TABLE Liedtext (id INTEGER PRIMARY KEY AUTOINCREMENT, lied_id INTEGER, ueberschrift TEXT, ueberschrifttyp TEXT, strophe TEXT, refrain_id INTEGER, reihenfolge INTEGER);
CREATE TABLE Refrain (id INTEGER PRIMARY KEY AUTOINCREMENT, refrainnr INTEGER, refrain TEXT, lied_id INTEGER);
CREATE TABLE Rubrik (id INTEGER PRIMARY KEY AUTOINCREMENT, rubrik TEXT, reihenfolge INTEGER);
