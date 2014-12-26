<?php
namespace Scotty\sqlitedbdump;

class TableDefinition
{

    private $sourceTable;

    private $destinationTable;

    private $columns;

    private function __construct($sourceTable, $destinationTable)
    {
        $this->sourceTable = $sourceTable;
        $this->destinationTable = $destinationTable;
    }

    public static function create($sourceTable)
    {
        return new TableDefinition($sourceTable, $sourceTable);
    }

    public static function createMapped($sourceTable, $destinationTable)
    {
        return new TableDefinition($sourceTable, $destinationTable);
    }

    public function column($sourceColumn)
    {
        $this->columns[$sourceColumn] = $sourceColumn;
        return $this;
    }

    public function columnMapped($sourceColumn, $destinationColumn)
    {
        $this->columns[$sourceColumn] = $destinationColumn;
        return $this;
    }

    public function getSourceTable()
    {
        return $this->sourceTable;
    }

    public function getDestinationTable()
    {
        return $this->destinationTable;
    }

    public function getAllSourceColumns()
    {
        $this->verifyColumnsExist();
        return array_keys($this->columns);
    }

    public function getAllDestinationColumns()
    {
        $this->verifyColumnsExist();
        return array_values($this->columns);
    }

    private function verifyColumnsExist()
    {
        if (! $this->columns) {
            throw new \Exception("Error in export table configuration of table $this->sourceTable. Table has no columns");
        }
    }
    
    public function buildSelectQuery(){
        $query = "SELECT " . implode(", ", $this->getAllSourceColumns()) . " FROM " . $this->getSourceTable();
        return $query;
    }
    
    public function buildInsertQuery($values){
        // TODO: Maybe prepared statements would be more performant.
        $query = "INSERT INTO " . $this->getDestinationTable() . " (" . implode(", ", $this->getAllDestinationColumns()) . ") VALUES ('" . $this->implodeEscaped($values) . "')";
        return $query;
    }
    
    private function implodeEscaped($values){
        foreach ($values as $v) {
        	$escapedValues[] = \SQLite3::escapeString($v);
        }
        return implode("', '", $escapedValues);
    }
}

?>