<?php
namespace Scotty\database;

use \Scotty\project\ProjectConfiguration;

class DatabaseConnector
{
	private static $instance;
	
	private $db;

	public function __construct()
	{
		$projectConfiguration = ProjectConfiguration::getInstance();
		$dbConf = $projectConfiguration->getDbConfiguration();
		$this->db = new \mysqli('127.0.0.1', $dbConf['user'], $dbConf['password'], $dbConf['schema']);

		$this->db->set_charset("utf8");
		if ($this->db->connect_error) {
			throw new DatabaseException("Database connection error: " . $this->db->connect_error);
		}
	}
	
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	public static function db()
	{
		return self::getInstance()->db;
	}

	public function __destruct()
	{
		$this->db->close();
	}
}