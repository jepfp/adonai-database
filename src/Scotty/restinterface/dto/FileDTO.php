<?php
namespace Scotty\restinterface\dto;

class FileDTO extends AbstractDTO
{

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("lied_id", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("data", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("filename", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("filesize", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("filetype", array(), array(
                $this->notNullOrEmpty()
            ))
        );
        parent::__construct($requestParams);
    }
}

