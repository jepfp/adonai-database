<?php
namespace Scotty\project;

class ProjectConfiguration{

	private static $instance;

	private $applicationRootPath;
	private $dataPath;

	private $projectLoader;
	private $printArrayDebugOutput = false;

	//default configuration
	private $defaultConfig = array(
			'projectName' => "CONFIGURE ME",
			'productiveMode' => false,
			'db' => array(
					'schema' => 'CONFIGURE ME',
					'user' => 'CONFIGURE ME',
					'password' => 'CONFIGURE ME'
			),
			'projectTitle' => "CONFIGURE ME",
			'defaultProjectName' => "example-scotty",
			'log4phpConfiguration' => array(
					'rootLogger' => array(
							'appenders' => array('default'),
							'level' => 'debug'
					),
					'loggers' => array(
							'dbLogger' => array(
									'appenders' => array('dbAppender'),
									'additivity' => false,
									'level' => 'debug'
							)
					),
					'appenders' => array(
							'default' => array(
									'class' => 'LoggerAppenderFile',
									'layout' => array(
											'class' => 'LoggerLayoutPattern',
											'params' => array(
													'conversionPattern' => '%date [%logger] [%level] %.2500message%newline%ex'
											)
									),
									'params' => array(
											'file' => 'CONFIGURE_ME',
											'append' => true
									)
							),
							'dbAppender' => array(
									'class' => 'LoggerAppenderPDO',
									'params' => array(
											'dsn' => 'CONFIGURE_ME',
											'user' => 'CONFIGURE_ME',
											'password' => 'CONFIGURE_ME',
											'table' => 'logging',
									)
							),
					)
			)
	);
	private $config;

	private $clientConfiguration = array(
			'projectName' => 'DETERMINE_ME',
			'projectTitle' => 'DETERMINE_ME',
			'user' => 'DETERMINE_ME'
	);

	private function __construct(){
		$this->determineBasePaths();
		$this->setupProjectConfiguration();
		$this->setupClientConfiguration();
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	private function determineBasePaths(){
		$this->applicationRootPath = realpath(__DIR__ . "/../../../");
		$this->dataPath = $this->getApplicationRootPath() . "/data";
	}

	private function setupProjectConfiguration(){
		$this->config = $this->defaultConfig;
		$this->printOutArray("Default Configuration:", $this->defaultConfig);
		$this->applyConfiguration($this->dataPath . "/globalConfiguration.ini");
		$this->printOutArray("Global over default:", $this->config);
		$this->projectLoader = new ProjectLoader($this->dataPath, $this->config['defaultProjectName']);
		$projectPath = $this->projectLoader->getProjectPath();
		$this->applyConfiguration($projectPath . "/config/projectConfiguration.ini", true);
		$this->printOutArray("Project over Global over default:", $this->config);
		//Determine a couple of config values programmatically
		$this->config['log4phpConfiguration']['appenders']['default']['params']['file'] = $this->getDefaultLogPath();
		$this->config['projectName'] = $this->projectLoader->getProjectName();
		$this->printOutArray("Programmatic over Project over Global over default:", $this->config);
	}

	/**
	 * @param boolean $mustExist If true, the script stops, if it can't find the configuration file. If false, it just moves on.
	 */
	private function applyConfiguration($path, $mustExist = false){
		if(!file_exists($path)){
			if($mustExist){
				echo "FATAL ERROR: " . $path . " does not exist. Stopping execution.";
				exit;
			}else{
				return;
			}
		}
		$ini_array = $this->parseIni($path);
		$this->config = array_replace_recursive($this->config, $ini_array);
	}

	private function parseIni($path){
		// Parse without sections
		$ini_array = parse_ini_file($path);

		//http://stackoverflow.com/questions/7480833/ini-file-to-multidimensional-array-in-php
		$ini = array();
		foreach($ini_array as $key => $value)
		{
			$p = &$ini;
			foreach(explode('.', $key) as $k)
				$p = &$p[$k];
			$p = $value;
		}
		unset($p);

		return $ini;
	}

	private function printOutArray($title, $array){
		if(!$this->printArrayDebugOutput){
			return;
		}
		echo "<b>$title</b><br>";
		echo "<pre>";
		print_r($array);
		echo "</pre><br>";
	}

	private function getDefaultLogPath(){
		return $this->projectLoader->getProjectPath() . "/log/main.log";
	}

	private function setupClientConfiguration(){
		$this->clientConfiguration['projectName'] = $this->getProjectName();
		$this->clientConfiguration['projectTitle'] = $this->getProjectTitle();
	}

	public function getApplicationRootPath(){
		return $this->applicationRootPath;
	}

	public function getProjectPath(){
		return $this->projectLoader->getProjectPath();
	}

	public function getLog4PhpConfiguration(){
		return $this->config['log4phpConfiguration'];
	}

	public function getWholeConfig(){
		return $this->config;
	}

	public function getProjectName(){
		return $this->config['projectName'];
	}

	public function getProjectTitle(){
		return $this->config['projectTitle'];
	}

	public function isProductiveMode(){
		return $this->config['productiveMode'];
	}

	public function getClientConfigurationJson(){
		if(isset($_SESSION["id"])){
			$this->clientConfiguration['user'] = array(
					"id" => $_SESSION["id"],
					"firstname" => $_SESSION["firstname"],
					"lastname" => $_SESSION["lastname"],
					"email" => $_SESSION["email"]
					);
		}else{
			$this->clientConfiguration['user'] = null;
		}
		return json_encode($this->clientConfiguration);
	}
	
	public function getDbConfiguration(){
		return $this->config['db'];
	}
}