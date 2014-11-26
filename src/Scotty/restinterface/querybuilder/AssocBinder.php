<?php
namespace Scotty\restinterface\querybuilder;

class AssocBinder
{
    
    // http://www.php.net/manual/en/mysqli-stmt.fetch.php#82742
    public static function bind(&$stmt, &$out)
    {
        $data = mysqli_stmt_result_metadata($stmt);
        $fields = array();
        $out = array();
        
        // Only do the binding, if we have a result set.
        //
        // From http://php.net/manual/en/mysqli-stmt.result-metadata.php#97338
        // If result_metadata() returns false but error/errno/sqlstate
        // tells you no error occurred, this means your query is one
        // that does not produce a result set, i.e. an INSERT/UPDATE/DELETE
        // query instead of a SELECT query.
        //
        if ($data === false) {
            return;
        }
        
        $count = 0;
        while ($field = mysqli_fetch_field($data)) {
            $fields[$count] = &$out[$field->name];
            $count ++;
        }
        call_user_func_array(array(
            $stmt,
            'bind_result'
        ), $fields);
    }
}

?>