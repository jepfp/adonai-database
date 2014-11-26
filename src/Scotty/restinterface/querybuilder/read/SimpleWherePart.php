<?php
namespace Scotty\restinterface\querybuilder\read;

class SimpleWherePart implements IWherePart
{

    private $part;

    public function getPart()
    {
        return $this->part;
    }
    
    public function getParamsToBind(){
        return array();
    }

    public function setPart($part)
    {
        $this->part = " " . $part . " ";
    }
}

?>