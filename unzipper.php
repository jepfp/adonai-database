<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$b_dir      = 'backup';
$zip        = 'zip.zip';
$exceptions = array(
		'.//' . $b_dir,
		'.//database',
		'.//client'
);

function delete_directory($dirname, $bool = false)
{
	global $zip, $b_dir, $exceptions;
	if (!file_exists($dirname))
		return true;
	if ($bool == false) {
		if (is_dir($dirname))
			$dir_handle = opendir($dirname);
		if (!$dir_handle)
			return false;
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname . "/" . $file))
					unlink($dirname . "/" . $file);
				else
					delete_directory($dirname . '/' . $file);
			}
		}
		closedir($dir_handle);
		@rmdir($dirname);
		return true;
	} else {
		if (is_dir($dirname))
			$dir_handle = opendir($dirname);
		if (!$dir_handle)
			return false;
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != ".." && $file != $zip) {
				if (!is_dir($dirname . "/" . $file)) {
					// echo $dirname.'/'.$file.'<br>';
					unlink($dirname . "/" . $file);
				} elseif (!in_array($dirname . '/' . $file, $exceptions)) {
					delete_directory($dirname . '/' . $file, true);
					//echo $dirname.'/'.$file.'<br>';    // .//backup.//database
				}
			}
		}
		closedir($dir_handle);
		if ($dirname != './')
			rmdir($dirname);
		// echo $dirname.'<br>';
		return true;
	}
}

function unzip($src_file, $dest_dir = './')
{
	$zip = new ZipArchive;
	$res = $zip->open($src_file);
	if ($res === TRUE) {
		$zip->extractTo($dest_dir);
		$zip->close();
		return true;
	} else {
		return false;
	}
}

function recurse_copy($src, $dst)
{
	global $b_dir, $zip;
	$dir = opendir($src);

	if ($dst != $b_dir . '/' . $b_dir)
		@mkdir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..') && ($file != $zip)) {
			if (is_dir($src . '/' . $file)) {
				recurse_copy($src . '/' . $file, $dst . '/' . $file);
			} else {
				@copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
	return true;
}

if (isset($_GET['p'])) {
	if ($_GET['p'] == 'restore') {
		if (file_exists($zip))
			echo 'ERROR: Backup already restored.';
		else {
			if (!delete_directory('./', true))
				echo 'ERROR: Directories could not be deleted!';
			if (!recurse_copy($b_dir . '/', './'))
				echo 'ERROR: Backup could not be created!';
			echo 'Files restored!';
		}
	}
} else {


	if (!file_exists($zip))
		echo 'ERROR: ZIP-File does not exist!';
	else {

		if (!delete_directory($b_dir))
			echo 'ERROR: Old backup directory could not be deleted!';
		if (!recurse_copy('./', $b_dir))
			echo 'ERROR: Backup could not be created!';

		if (!delete_directory('./', true))
			echo 'ERROR: Directories could not be deleted!';

		if (!unzip($zip))
			echo 'ERROR: Unzip fehlgeschlagen!';
		$t = time();
		if (!rename($zip, date("Y-m-d-H-i-s", $t) . '.zip'))
			echo 'ERROR: Zip-File konnte nicht umbenannt werden!';

		copy('./' . $b_dir . '/unzipper.php', './unzipper.php');

		echo 'ZIP-File:"' . date("Y-m-d-H-i-s", $t) . '.zip"';
	}

}

?>