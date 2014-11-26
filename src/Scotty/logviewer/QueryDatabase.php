<?php
namespace Scotty\logviewer;

use \Scotty\database\DatabaseConnector;

class QueryDatabase
{
	private $projectConfiguration;
	
	public function getResults(\stdClass $params)
	{
		$db = DatabaseConnector::db();
		$_result = $db->query("SELECT id, timestamp, file, REPLACE(message, '\n', '<br>\n') as message FROM logging") or die('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error);

		$results = array();

		while ($row = $_result->fetch_assoc()) {
			array_push($results, $row);
		}

		return $results;
	}
}