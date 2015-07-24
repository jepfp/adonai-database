<?php
namespace Scotty\restinterface\dao;

use Scotty\file\FileHelper;
use Scotty\file\FileMetadataHelper;

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

    protected function onBeforeBuildCreateQuery()
    {
        $fileMetadataId = $this->createFileMetadataEntryForNewFile();
        $this->replaceLiedIdByFileMetadataIdInParams($fileMetadataId);
    }

    private function createFileMetadataEntryForNewFile()
    {
        $params = $this->request->params;
        $this->verifyLiedIdIsSet($params);
        $liedId = $params['lied_id'];
        $fileMetadataId = FileMetadataHelper::createSourcePdfFileMetadataEntry($liedId);
        $this->logger->debug("New filemetadata entry with id " . $fileMetadataId . " and lied_id " . $liedId . " inserted.");
        return $fileMetadataId;
    }

    private function verifyLiedIdIsSet($params)
    {
        if ($params == null || $params['lied_id'] < 1) {
            throw new \RuntimeException("File needs to know the lied_id in order to create a filemetadata entry. Please provide lied_id param!");
        }
    }

    private function replaceLiedIdByFileMetadataIdInParams($fileMetadataId)
    {
        $params = &$this->request->params;
        unset($params['lied_id']);
        $params['filemetadata_id'] = $fileMetadataId;
    }
    
    protected function redirectToGETWithId(&$res, $id)
    {
        // nothing to do so far
        $a = 2;
    }
}

