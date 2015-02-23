<?php
namespace Scotty\restinterface\dao;

use Scotty\file\FileHelper;

class FileDAO extends AbstractDAO
{

    protected function onBeforeExecuteRead()
    {
        $this->throwExceptionIfNoId();
    }

    private function throwExceptionIfNoId()
    {
        if (! $this->request->id > 0) {
            throw new \RuntimeException("File does not support fetching all entries. Please filter by id.");
        }
    }

    protected function transformResult($row)
    {
        $row["builtFilename"] = FileHelper::buildFilenameByLied($row["lied_id"]) . "." . $row["filetype"];
        return $row;
    }
}

