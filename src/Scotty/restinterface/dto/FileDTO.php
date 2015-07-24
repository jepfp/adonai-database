<?php
namespace Scotty\restinterface\dto;

class FileDTO extends AbstractDTO
{

    /**
     * For now only file upload html input elements with name = file are taken into account.
     * See FileDTO_FileInputName.png for details (this folder).
     */
    const NAME_OF_HTML_INPUT_ELEMENT = 'file';

    private static $MIME_MAPPING = array(
        'application/pdf' => 'pdf'
    );

    public function __construct($requestParams)
    {
        $this->fields = array(
            Field::create("filemetadata_id", array(), array(
                $this->notNullOrEmpty()
            )),
            Field::create("filename", array(
                self::extractFilenameFromMultipartFormDataRequest()
            ), array(
                $this->notNullOrEmpty()
            )),
            Field::create("filesize", array(
                self::extractFilesizeFromMultipartFormDataRequest()
            ), array(
                $this->notNullOrEmpty()
            )),
            Field::create("filetype", array(
                self::extractAndMapFileTypeFromMultipartFormDataRequest()
            ), array(
                $this->notNullOrEmpty()
            )),
            Field::create("data", array(
                self::extractFileDataFromMultipartFormDataRequest()
            ), array(
                $this->notNullOrEmpty()
            ))
        );
        parent::__construct($requestParams);
    }

    private static function extractFileDataFromMultipartFormDataRequest()
    {
        return function ($input) {
            $content = self::readUploadedFileContent();
            return $content;
        };
    }

    private static function readUploadedFileContent()
    {
        try {
            $tmpName = $_FILES[self::NAME_OF_HTML_INPUT_ELEMENT]['tmp_name'];
            self::verifyFileExists($tmpName);
            $fp = fopen($tmpName, 'r');
            return fread($fp, filesize($tmpName));
        } catch (\Exception $x) {
            throw new DTOException("Die Datei konnte nicht gelesen werden.");
        }
    }

    private static function verifyFileExists($path)
    {
        if (! file_exists($path)) {
            throw new \RuntimeException("File does not exist.");
        }
    }

    private static function extractFilenameFromMultipartFormDataRequest()
    {
        return function ($input) {
            return $_FILES[self::NAME_OF_HTML_INPUT_ELEMENT]['name'];
        };
    }

    private static function extractFilesizeFromMultipartFormDataRequest()
    {
        return function ($input) {
            return $_FILES[self::NAME_OF_HTML_INPUT_ELEMENT]['size'];
        };
    }

    private static function extractAndMapFileTypeFromMultipartFormDataRequest()
    {
        return function ($input) {
            $type = $_FILES[self::NAME_OF_HTML_INPUT_ELEMENT]['type'];
            return self::$MIME_MAPPING[$type];
        };
    }
}

