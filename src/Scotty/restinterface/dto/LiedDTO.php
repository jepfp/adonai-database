<?php
namespace Scotty\restinterface\dto;

class LiedDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("Titel", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("rubrik_id", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("lastEditUser_id", array(
                StandardTransformers::determineCurrentUserId()
            ), array(
                $this->notNullOrEmpty()
            )),
            Field::create("tonality", array(
                StandardTransformers::emptyToNull()
            ), array())
        );
        parent::__construct($requestParams);
    }

    protected function getFieldsSetByApplication()
    {
        $fieldsSetByApp = parent::getFieldsSetByApplication();
        $fieldsSetByApp[] = "lastEditUser_id";
        return $fieldsSetByApp;
    }

    protected function getFieldsToIgnore()
    {
        $fieldsToIgnore = parent::getFieldsToIgnore();
        $fieldsToIgnore[] = "file_id";
        return $fieldsToIgnore;
    }
}

