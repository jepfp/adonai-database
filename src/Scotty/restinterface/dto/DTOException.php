<?php
namespace Scotty\restinterface\dto;

class DTOException extends \RuntimeException
{
    private $fieldName;
    public function __construct($message){
        parent::__construct($message);
    }
    
    public function getFieldName(){
        return $this->fieldName;
    }
    
    public function setFieldName($value){
        $this->fieldName = $value;
    }
}