<?php
namespace Scotty\restinterface\querybuilder;

// according to http://www.php.net/manual/de/mysqli-stmt.bind-param.php#109256
class BindParam
{

    private $values = array();

    private $types = '';

    /**
     *
     * @param String $type
     *            The following types are possible:<br>
     *            i	corresponding variable has type integer<br>
     *            d	corresponding variable has type double<br>
     *            s	corresponding variable has type string<br>
     *            b	corresponding variable is a blob and will be sent in packets
     * @param String $value            
     */
    public function add($type, $value)
    {
        $this->values[] = $value;
        $this->types .= $type;
    }

    public function get()
    {
        $mergedArray = array_merge(array(
            $this->types
        ), $this->values);
        return $mergedArray;
    }

    public function isEmpty()
    {
        if (count($this->values) === 0) {
            return true;
        } else {
            return false;
        }
    }
} 

