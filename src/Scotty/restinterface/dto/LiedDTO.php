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
            new Field("tonality")
        );
        parent::__construct($requestParams);
    }
}

