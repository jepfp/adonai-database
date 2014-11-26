@ECHO OFF
if not exist import_sqlite_db\adonai.sqlite (
    echo Could not find the import database: import_sqlite_db/adonai.sqlite. Stopping batch.
	if not exist import_sqlite_db mkdir import_sqlite_db
	GOTO:EOF
)
if not exist export mkdir export
rem The following error doesn't bother us: "Error: unknown command or invalid arguments:  "quit;". Enter ".help" for help"
sqlite3.exe import_sqlite_db/adonai.sqlite -init exportCommands.sql
cd export
copy /b rubrik.sql+lied.sql+liederbuch.sql+fkliederbuchlied.sql+refrain.sql+liedtext.sql+settings.sql MERGED.sql
cd..