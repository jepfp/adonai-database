<?php
namespace Scotty\restinterface\dto;

class LiedViewDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("Liednr", array(
                StandardTransformers::emptyToNull()
            ), array()
            // $this->notNullOrEmpty() -> It may be null
            ),
            Field::create("lied_id", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("liederbuch_id", array(), array(
                StandardTransformers::determineCurrentLiederbuchId()
            ))
        );
        parent::__construct($requestParams);
    }

    protected function getFieldsToIgnore()
    {
        $fieldsToIgnore = parent::getFieldsToIgnore();
        $fieldsToIgnore[] = "Rubrik";
        $fieldsToIgnore[] = "Titel";
        $fieldsToIgnore[] = "tonality";
        $fieldsToIgnore[] = "email";
        return $fieldsToIgnore;
    }
}

