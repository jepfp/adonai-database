<?php

require_once '../src/bootstrap.php';

require_once(dirname(__FILE__).'/../symfony/config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('webservice', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
