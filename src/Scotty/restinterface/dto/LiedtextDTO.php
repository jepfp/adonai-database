<?php
namespace Scotty\restinterface\dto;

class LiedtextDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("Strophe", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("refrain_id", array(
                StandardTransformers::number0ToNull()
            ), array()),
            Field::create("lied_id", array(), array(
                $this->notNullOrEmpty()
            ))
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

