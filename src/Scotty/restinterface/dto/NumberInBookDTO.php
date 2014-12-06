<?php
namespace Scotty\restinterface\dto;

class NumberInBookDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("Liednr", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("lied_id", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("liederbuch_id", array(), array(
                $this->notNullOrEmpty()
            ))
        );
        parent::__construct($requestParams);
    }
    
    protected function getFieldsToIgnore()
    {
        $fieldsToIgnore = parent::getFieldsToIgnore();
        $fieldsToIgnore[] = "Beschreibung";
        $fieldsToIgnore[] = "Buchname";
        $fieldsToIgnore[] = "locked";
        $fieldsToIgnore[] = "mnemonic";
        return $fieldsToIgnore;
    }
}

