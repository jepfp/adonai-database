<?php
namespace Scotty\restinterface;

use Scotty\restinterface\dao\AbstractDAO;
class DAOFactory
{

    private static $logger;

    private static function getLogger()
    {
        if (! self::$logger) {
            self::$logger = \Logger::getLogger("main");
        }
        return self::$logger;
    }

    /**
     * 
     * @param String $controller
     * @throws \RuntimeException If the controller can not be created.
     * @return AbstractDAO Concrete subclass of AbstractDAO
     */
    public static function createDAO($controller)
    {
        $controllerName = ucfirst($controller) . "DAO";
        $fullyQualifiedDaoController = "\\Scotty\\restinterface\\dao\\" . $controllerName;
        if (! class_exists($fullyQualifiedDaoController)) {
            $message = "Controller '" . $controller . "' does not exist.";
            throw new \RuntimeException($message);
        }
        self::getLogger()->trace("Instantiating controller " . $fullyQualifiedDaoController);
        $dao = new $fullyQualifiedDaoController();
        return $dao;
    }
}

?>