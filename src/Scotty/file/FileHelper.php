<?php
namespace Scotty\file;

use \Scotty\database\DatabaseConnector;

class FileHelper
{

    public static function findFileIdByLiedId($liedId)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("SELECT f.id as id FROM `file` as f join filemetadata as md ON (f.filemetadata_id = md.id) WHERE md.lied_id = ?;");
        $statement->bind_param("i", $liedId);
        $statement->execute();
        $statement->bind_result($id);
        if ($statement->fetch() === true) {
            return $id;
        } else {
            return null;
        }
    }

    public static function buildFilenameByFileId($fileId)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("SELECT Titel FROM lied as l join filemetadata as md ON (l.id = md.lied_id) join `file` as f ON (md.id = f.filemetadata_id) WHERE f.id = ?;");
        $statement->bind_param("i", $fileId);
        $statement->execute();
        $statement->bind_result($titel);
        if ($statement->fetch() === true) {
            return self::stringToValidFilename($titel);
        } else {
            throw new \RuntimeException("Titel for file with id " . $fileId . " could not be generated.");
        }
    }

    public static function stringToValidFilename($string)
    {
        return preg_replace("/[^0-9^a-z^A-Z^_^.^ ^ä^ö^ü^Ä^Ö^Ü]/", "", $string);
    }
}