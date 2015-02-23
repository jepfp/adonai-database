<?php
namespace Scotty\file;

use \Scotty\database\DatabaseConnector;

class FileHelper
{

    public static function findFileIdByLiedId($liedId)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("SELECT id FROM file WHERE lied_id = ?");
        $statement->bind_param("i", $liedId);
        $statement->execute();
        $statement->bind_result($id);
        if ($statement->fetch() === true) {
            return $id;
        } else {
            return null;
        }
    }

    public static function buildFilenameByLied($liedId)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("SELECT Titel FROM lied WHERE id = ?");
        $statement->bind_param("i", $liedId);
        $statement->execute();
        $statement->bind_result($titel);
        if ($statement->fetch() === true) {
            return self::stringToValidFilename($titel);
        } else {
            throw new \RuntimeException("Lied with id " . $liedId . " does not exist.");
        }
    }

    public static function stringToValidFilename($string)
    {
        return preg_replace("/[^0-9^a-z^A-Z^_^.^ ^ä^ö^ü^Ä^Ö^Ü]/", "", $string);
    }
}