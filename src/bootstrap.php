<?php
session_start();
require_once(__DIR__ . "/lib/Symfony/Component/ClassLoader/UniversalClassLoader.php");
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace("Scotty", __DIR__);
$loader->register();

$projectConfiguration = Scotty\project\ProjectConfiguration::getInstance();
require_once(__DIR__ . "/lib/log4php/Logger.php");
Logger::configure($projectConfiguration->getLog4PhpConfiguration());
$logger = Logger::getLogger("main");
$logger->trace("New request. Logger ready. bootstrap.php located at " . __FILE__);
$dbLogger = Logger::getLogger("dbLogger");
$dbLogger->trace("New request. Logger ready. Db Logger (project: " . $projectConfiguration->getProjectTitle() . ") ready.");

