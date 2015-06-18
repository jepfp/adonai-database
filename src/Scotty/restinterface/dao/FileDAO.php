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
        $filters = $this->request->getRequestParamAsDecodedJson("filter");
        if ($filters == null || $filters[0]->property != "filemetadata_id") {
            // ($filtres == null) is also true for an empty filter array
            throw new \RuntimeException("File does not support fetching all entries. Please filter by filemetadata_id.");
        }
    }

    protected function transformResult($row)
    {
        $row["builtFilename"] = FileHelper::buildFilenameByFileId($row["id"]) . "." . $row["filetype"];
        return $row;
    }
}

