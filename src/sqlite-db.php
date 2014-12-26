<?php
use Scotty\sqlitedbdump\SQLiteDbDumper;
require ('bootstrap.php');

$dumper = new SQLiteDbDumper();
$path = $dumper->performExport();
if (file_exists($path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=adonai.sqlite');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
}

?>
