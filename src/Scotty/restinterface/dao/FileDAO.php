<?php
namespace Scotty\restinterface\dao;

use Scotty\file\FileHelper;
use Scotty\file\FileMetadataHelper;
use Scotty\restinterface\dto\DTOException;
use Scotty\restinterface\dto\Scotty\restinterface\dto;
use Scotty\exception\DomainException;

class FileDAO extends AbstractDAO
{

    /**
     * For now only file upload html input elements with name = file are taken into account.
     * See FileDTO_FileInputName.png for details (this folder).
     */
    const NAME_OF_HTML_INPUT_ELEMENT = 'file';

    const PDF_MIME_TYPE = 'application/pdf';

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
        $this->validateUploadedFile();
        $liedId = $this->validateAndGetLiedId();
        $this->verifyNoOtherSourcePdfExists($liedId);
        $fileMetadataId = $this->createFileMetadataEntryForNewFile($liedId);
        $this->replaceLiedIdByFileMetadataIdInParams($fileMetadataId);
    }

    private function validateUploadedFile()
    {
        $this->verifyFileSent();
        $this->verifiyMaxFileSize();
        $this->verifyNoUploadError();
        $this->verifyType();
    }

    private function verifyFileSent()
    {
        if (! isset($_FILES[FileDAO::NAME_OF_HTML_INPUT_ELEMENT])) {
            // If this exception occurs, maybe php is configured wrong? Check
            // http://stackoverflow.com/questions/9691057/php-apache-ajax-post-limit
            $ex = new DomainException('Es wurde keine hochgeladene Datei gefunden. Eventuell ist die Datei zu gross oder es liegt ein Server-Konfigurationsfehler vor.');
            throw $ex;
        }
    }
    
    // Note: This method is not tested with an int test, because post_max_size is caught before.
    private function verifiyMaxFileSize()
    {
        $errorCode = $_FILES[FileDAO::NAME_OF_HTML_INPUT_ELEMENT]['error'];
        if ($errorCode === UPLOAD_ERR_INI_SIZE) {
            throw new DomainException("Die Datei ist zu gross.");
        }
    }
    
    // Note: This method is not tested with an int test, because it cannot be faked.
    private function verifyNoUploadError()
    {
        $errorCode = $_FILES[FileDAO::NAME_OF_HTML_INPUT_ELEMENT]['error'];
        if ($errorCode !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("PHP has set the error code '$errorCode' uploaded file. Please check the error in the php documentation.");
        }
    }

    private function verifyType()
    {
        $type = $_FILES[FileDAO::NAME_OF_HTML_INPUT_ELEMENT]['type'];
        if ($type != self::PDF_MIME_TYPE) {
            throw new DomainException("Die hochgeladene Datei ist keine PDF-Datei.");
        }
    }

    private function validateAndGetLiedId()
    {
        $params = $this->request->params;
        $this->verifyLiedIdIsSet($params);
        $liedId = $params['lied_id'];
        return $liedId;
    }

    private function verifyNoOtherSourcePdfExists($liedId)
    {
        $amount = FileMetadataHelper::countSourcePdfForLied($liedId);
        if ($amount !== 0) {
            throw new DomainException("Fehler beim Hochladen der Noten, weil bereits Noten zu diesem Lied vorhanden sind. Bitte aktualisiere die Seite und lösche zuerst vorhandene Noten.");
        }
    }

    private function createFileMetadataEntryForNewFile($liedId)
    {
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
    }
}

