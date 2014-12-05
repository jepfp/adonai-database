<?php
namespace Scotty\restinterface\dto;

class RefrainDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("Refrain", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("lied_id", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("Reihenfolge", array(
                StandardTransformers::timeInSecondsSinceFirstOfYear1970()
            ), array())
        );
        parent::__construct($requestParams);
    }

    protected function getFieldsToIgnore()
    {
        $fieldsToIgnore = array(
            "language_id"
        );
        return $fieldsToIgnore;
    }
}

