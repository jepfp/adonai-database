<?php
namespace Scotty\restinterface\querybuilder\read;

interface IWherePart
{
    public function getPart();
    
    public function getParamsToBind();
}

?>