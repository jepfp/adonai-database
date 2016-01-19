<?php
namespace Scotty\sqlitedbdump;

use Scotty\project\ProjectConfiguration;
use Scotty\filesystem\FsHelper;
use Scotty\database\DbHelper;
use Scotty\database\DatabaseConnector;
use Scotty\utils\StopWatch;
use Scotty\sqlitedbdump\songbooksongnumberassociation\CrossAssociationFiller;

class SQLiteDbDumper
{

    private $db;

    private $sqliteDb;

    private $path = "/sqliteDbDump/";

    private $filename = "adonai.sqlite";

    private $projectConfiguration;

    private $tableDefinitions;

    private $logger;

    public function __construct()
    {
        $this->projectConfiguration = ProjectConfiguration::getInstance();
        $this->logger = \Logger::getLogger("main");
        
        $this->buildTableDefinitions();
        $this->db = DatabaseConnector::db();
    }

    private function buildTableDefinitions()
    {
        $this->tableDefinitions[] = TableDefinition::create("lied")->column("id")
            ->column("Titel")
            ->column("rubrik_id")
            ->column("created_at")
            ->column("updated_at");
        $this->tableDefinitions[] = TableDefinition::create("language")->column("id")
            ->column("code")
            ->column("name");
        $this->tableDefinitions[] = TableDefinition::create("fkliederbuchlied")->column("id")
            ->column("liederbuch_id")
            ->column("lied_id")
            ->column("Liednr");
        $this->tableDefinitions[] = TableDefinition::create("liederbuch")->column("id")
            ->column("Buchname")
            ->column("Beschreibung")
            ->column("mnemonic");
        $this->tableDefinitions[] = TableDefinition::create("liedtext")->column("id")
            ->column("lied_id")
            ->column("Strophe")
            ->column("refrain_id")
            ->column("Reihenfolge")
            ->column("language_id");
        $this->tableDefinitions[] = TableDefinition::create("refrain")->column("id")
            ->column("lied_id")
            ->columnMapped("Reihenfolge", "RefrainNr")
            ->column("Refrain")
            ->column("language_id");
        $this->tableDefinitions[] = TableDefinition::create("rubrik")->column("id")
            ->column("Rubrik")
            ->column("Reihenfolge");
    }

    /**
     * Exports the mysql database to a new sqlite database according to the configuration and
     * returns the path to the created sqlite database.
     */
    public function performExport()
    {
        $this->logger->info("Starting export of sqlite db. Starting stop watch now.");
        $stopWatch = StopWatch::start();
        $this->logger->trace("Sqlite db path will be " . $this->buildDbFilePath());
        $this->deleteOldDb();
        $this->createNewDbFromContractDatabase();
        $this->performDbOperations();
        $this->logger->info("Sqlite db export finished after " . $stopWatch->measure() . " seconds.");
        return $this->buildDbFilePath();
    }

    private function deleteOldDb()
    {
        FsHelper::createFolderIfNotExists($this->buildDbFolderPath());
        $filePath = $this->buildDbFilePath();
        unlink($filePath);
        if (file_exists($filePath)) {
            // File must not exist after deletion because we don't want to reopen an existing database
            throw new \RuntimeException("Could not delete and recreate the sqlite database. Maybe it's locked at the moment. Try again.");
        }
    }

    private function createNewDbFromContractDatabase()
    {
        if (! copy(__DIR__ . "/emptyContractDatabase.sqlite", $this->buildDbFilePath())) {
            throw new \RuntimeException("Could setup empty database from contract database");
        }
    }

    private function performDbOperations()
    {
        $this->sqliteDb = new \SQLite3($this->buildDbFilePath());
        $this->sqliteDb->exec("BEGIN IMMEDIATE TRANSACTION");
        $this->exportAllMySqlTables();
        $this->addCrossSongbookSongnumberAssociations();
        //Hier die weiteren Liedernummern ergänzen
        $this->sqliteDb->exec("COMMIT TRANSACTION");
        $this->sqliteDb->close();
    }
    
    private function exportAllMySqlTables()
    {
        foreach ($this->tableDefinitions as $td) {
            $this->exportTable($td);
        }
    }
    
    private function addCrossSongbookSongnumberAssociations()
    {
        $filler = new CrossAssociationFiller();
        $filler->fill($this->db, $this->sqliteDb);
    }

    private function exportTable($tableDefinition)
    {
        $this->logger->trace("Exporting table " . $tableDefinition->getSourceTable() . " -> " . $tableDefinition->getDestinationTable());
        $query = $tableDefinition->buildSelectQuery();
        $this->logger->trace("Select query is: " . $query);
        $result = $this->db->query($query);
        DbHelper::throwExceptionOnError($result, $this->db, $query);
        while ($row = $result->fetch_row()) {
            $insertQuery = $tableDefinition->buildInsertQuery($row);
            $querySuccessful = $this->sqliteDb->exec($insertQuery);
            if (! $querySuccessful || $this->sqliteDb->lastErrorCode() !== 0) {
                throw new \RuntimeException("Error while executing statement against sqlite database: " . $this->sqliteDb->lastErrorMsg() . "\nQuery is: " . $insertQuery);
            }
        }
        $result->close();
    }

    private function buildDbFilePath()
    {
        return $this->buildDbFolderPath() . $this->filename;
    }

    private function buildDbFolderPath()
    {
        return $this->projectConfiguration->getProjectPath() . $this->path;
    }
}

?>