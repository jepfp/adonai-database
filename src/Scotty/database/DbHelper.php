<?php
namespace Scotty\database;

class DbHelper
{
    /**
     * @param unknown $checkSubject statement or query result
     */
    public static function throwExceptionOnError($checkSubject, $db, $query)
    {
        if ($checkSubject === false) {
            // false if an error occurred
            throw DatabaseException::constructWithDbErrorAndQuery($db->error, $query);
        }
    }
    
    /**
     * Throws an error if the statement has an error.
     * @param mysqli_stmt $statement
     * @throws DatabaseException
     */
    public static function throwExceptionOnStatementError($statement)
    {
        if ($statement->sqlstate != "00000") {
            throw DatabaseException::constructFromStatement($statement);
        }
    }
    
    public static function bindParams($statement, $bindParamObject)
    {
        // The following has been done with this comment:
        // http://www.php.net/manual/en/mysqli-stmt.bind-param.php#104073
        // Im Moment weiss ich nicht, warum es die refValues-Funktion braucht.
        // Eigentlich sollte der Referenzparameter in BindParam->add bereits ausreichen.
        // Dies reicht aber weder mithilfe von call_user_func_array, noch mit ReflectionClass.
        if (! $bindParamObject->isEmpty()) {
            $reflection = new \ReflectionClass("mysqli_stmt");
            $method = $reflection->getMethod("bind_param");
            $refArray = self::refValues($bindParamObject->get());
            $method->invokeArgs($statement, $refArray);
        }
    }
    
    private static function refValues($arr)
    {
        $refs = array();
        foreach ($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
}

?>