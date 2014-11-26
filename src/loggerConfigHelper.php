<?php
/* Not everywhere the examples for the logger are also provided
 * as php config array. For this case we can translate an xml configuration
 * to a php configuration via this simple code.
 */

require_once(__DIR__ . "/lib/log4php/Logger.php");
$configurator = new LoggerConfiguratorDefault();
$config = $configurator->parse('exampleConfig.xml');
echo "<pre>";
print_r($config);
echo "</pre>";