<?php
namespace Scotty\restinterface;

class DynamicClassResolver
{

    private static $logger;

    private static function getLogger()
    {
        if (! self::$logger) {
            self::$logger = \Logger::getLogger("main");
        }
        return self::$logger;
    }

    public static function resolveDtoClass($controller)
    {
        $fullyQualifiedName = __NAMESPACE__ . "\\dto\\" . ucfirst($controller) . "DTO";
        self::getLogger()->trace("Resolved DTO class " . $fullyQualifiedName);
        self::throwExceptionOnNonexistentClass($fullyQualifiedName);
        return $fullyQualifiedName;
    }

    public static function throwExceptionOnNonexistentClass($fullyQualifiedName)
    {
        if (! class_exists($fullyQualifiedName)) {
            $message = "DynamicClassResolver: class '$fullyQualifiedName' not found!";
            self::getLogger()->error($message);
            throw new \RuntimeException($message);
        }
    }
}

?>