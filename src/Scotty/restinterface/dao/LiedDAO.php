<?php
namespace Scotty\restinterface\dao;

use Scotty\file\FileHelper;

class LiedDAO extends AbstractDAO
{

    protected function transformResult($row)
    {
        $row = parent::transformResult($row);
        $row["file_id"] = FileHelper::findFileIdByLiedId($row["id"]);
        return $row;
    }
}

