<?php
namespace Scotty\project;

Class ProjectLoader
{
	private $allProjectsPath;
	private $defaultProjectName;
	private $projectName;
	private $projectPath;

	public function __construct($dataPath, $defaultProjectName){
		$this->allProjectsPath = $dataPath . "/projects";
		$this->defaultProjectName = $defaultProjectName;
		
		$this->determineProjectNameAndPath();
	}
	
	private function determineProjectNameAndPath(){
		
		$this->projectName = $this->getSubdomain();
		$this->projectPath = $this->allProjectsPath . "/" . $this->projectName;
		if(!file_exists($this->projectPath)){
			$this->projectName = $this->defaultProjectName;
			$this->projectPath = $this->allProjectsPath . "/" . $this->projectName;
		}
	}

	public final function getProjectPath(){
		return $this->projectPath;
	}
	
	public function getProjectName(){
		return $this->projectName;
	}
	
	public function getSubdomain(){
		$domainParts = explode(".",$_SERVER['HTTP_HOST']);
		return $domainParts[0];
	}
}
