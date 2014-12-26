<?php
namespace Scotty\filesystem;

class FsHelper
{
    public static function createFolderIfNotExists($folderPath)
    {
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }
    
}

?>