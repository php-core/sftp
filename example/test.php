<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('max_execution_time', 0);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPCore\SFTP\SFTP as Sftp;

$FtpServer = 'sftp.your.host.com';
$FtpPort = 22;
$FtpUser = 'username';
$FtpPass = 'password';
$FtpPath = '/home/web/public_html';


# Scan Directory
$test = SFTP::scandir($FtpServer, $FtpUser, $FtpPass, $FtpPath, $FtpPort);
echo "scandir";
var_dump($test);

# Test connection
$test = SFTP::test($FtpServer, $FtpUser, $FtpPass, $FtpPort);
echo "test";
var_dump($test);

# Check Login Directory PWD
$test = SFTP::pwd($FtpServer, $FtpUser, $FtpPass, $FtpPort);
echo "pwd";
var_dump($test);



# Upload File
$local_file = __DIR__ . '/test.txt';
$remote_file = $FtpPath . '/test.txt';
$test = SFTP::upload($FtpServer, $FtpUser, $FtpPass, $local_file, $remote_file, $FtpPort);
echo "upload";
var_dump($test);

# Download File
$local_file = __DIR__ . '/test-download.txt';
$remote_file = $FtpPath . '/test.txt';
$test = SFTP::download($FtpServer, $FtpUser, $FtpPass, $remote_file, $local_file, $FtpPort);
echo "download";
var_dump($test);
unlink($local_file);

# Rename File
$old_file = $FtpPath . '/test.txt';
$new_file = $FtpPath . '/test-renamed.txt';
$test = SFTP::rename($FtpServer, $FtpUser, $FtpPass, $old_file, $new_file, $FtpPort);
echo "rename file";
var_dump($test);

# Test file exist
$remote_file = $FtpPath . '/test-renamed.txt';
$test = SFTP::isFile($FtpServer, $FtpUser, $FtpPass, $remote_file, $FtpPort);
echo "is_file";
var_dump($test);

# Delete File
$remote_file = $FtpPath . '/test-renamed.txt';
$test = SFTP::delete($FtpServer, $FtpUser, $FtpPass, $remote_file, $FtpPort);
echo "delete file";
var_dump($test);



# Upload Folder
// if ends with a slash upload content
// if no slash end upload dir itself
$local_path = __DIR__ . '/../src';
$remote_path = $FtpPath;
$test = SFTP::uploadDir($FtpServer, $FtpUser, $FtpPass, $local_path, $remote_path, $FtpPort);
echo "upload_dir";
var_dump($test);

# Download Folder
// if ends with a slash download content
// if no slash end download dir itself
$remote_dir = $FtpPath . '/src';
$local_dir = __DIR__;
$test = SFTP::downloadDir($FtpServer, $FtpUser, $FtpPass, $remote_dir, $local_dir, $FtpPort);
var_dump($test);

# Delete Folder
// if ends with a slash delete content
// if no slash delete dir itself
$remote_path = $FtpPath . '/src';
$test = SFTP::rmdir($FtpServer, $FtpUser, $FtpPass, $remote_path, $FtpPort);
var_dump($test);




# Create File
$file_name = $FtpPath . '/test.txt';
$file_content = 'Love it !';
$test = SFTP::touch($FtpServer, $FtpUser, $FtpPass, $file_name, $file_content, $FtpPort);
echo "touch";
var_dump($test);

# Delete File
$remote_file = $FtpPath . '/test.txt';
$test = SFTP::delete($FtpServer, $FtpUser, $FtpPass, $remote_file, $FtpPort);
echo "delete file";
var_dump($test);



# Create Folder
$directory = $FtpPath . '/coucou';
$test = SFTP::mkdir($FtpServer, $FtpUser, $FtpPass, $directory, $FtpPort);
echo "mkdir";
var_dump($test);

# Rename Folder
$old_file = $FtpPath . '/coucou';
$new_file = $FtpPath . '/coco';
$test = SFTP::rename($FtpServer, $FtpUser, $FtpPass, $old_file, $new_file, $FtpPort);
echo "rename dir";
var_dump($test);

# Delete Folder
$remote_path = $FtpPath . '/coco';
$test = SFTP::rmdir($FtpServer, $FtpUser, $FtpPass, $remote_path, $FtpPort);
echo "rmdir";
var_dump($test);



# Scan directory again
$test = SFTP::scandir($FtpServer, $FtpUser, $FtpPass, $FtpPath, $FtpPort);
echo "scandir";
var_dump($test);
