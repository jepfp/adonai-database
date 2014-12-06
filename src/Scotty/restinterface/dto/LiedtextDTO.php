<?php
namespace Scotty\restinterface\dto;

class LiedtextDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("Strophe", array(), array()),
            Field::create("refrain_id", array(
                StandardTransformers::number0ToNull()
            ), array()),
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
        $fieldsToIgnore = parent::getFieldsToIgnore();
        $fieldsToIgnore[] = "language_id";
        return $fieldsToIgnore;
    }
}

