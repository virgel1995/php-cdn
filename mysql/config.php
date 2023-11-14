<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
$server_url = "http://localhost";
$process_urls = array(
    'download' => '/cdn/mysql/download',
    'view' => '/cdn/mysql/view',
    'upload' => '/cdn/mysql/upload/index.php',
);
// main directory
$targetDirectory = "../uploads/";
// sub directorys
$allowedBase64 = array('images', 'pdf');
$mediaFiles = array('audio', 'video');
$applications = array('desktop-app' , 'word' , 'excel' , 'pdf' , 'compress' , 'other');

$db_host = "localhost"; // Change to your database host if necessary
$db_user = "root";
$db_port = '3306';
$db_pass = "";
$db_name = "cdn";

$connection = new mysqli($db_host, $db_user, $db_pass);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($connection->query($sql) !== TRUE) {
    echo "Error creating database: " . $connection->error;
}
$connection = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
$sql = "CREATE TABLE IF NOT EXISTS `$db_name`.`files` 
(
`id` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255) NULL , 
`original_name` VARCHAR(255) NULL , 
`path` VARCHAR(255) NOT NULL ,
`size` VARCHAR(255) NOT NULL , 
`type` VARCHAR(255) NOT NULL ,
`mime_type` VARCHAR(255) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE = InnoDB;";
$result = $connection->query($sql);

function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . $units[$pow];
}

function process_SubDirectory($fileType)
{
    $subDirectory = '';
    if (in_array($fileType, array('jpg', 'jpeg', 'png', 'gif'))) {
        $subDirectory = 'images/';
    } elseif (in_array($fileType, array('mp4', 'avi', 'mkv'))) {
        $subDirectory = 'video/';
    } elseif (in_array($fileType, array('mp3', 'wav'))) {
        $subDirectory = 'audio/';
    } elseif (in_array($fileType, array('pdf'))) {
        $subDirectory = 'pdf/';
    } elseif (in_array($fileType, array('docx', 'doc'))) {
        $subDirectory = 'word/';
    } elseif (in_array($fileType, array('xla', 'xlsx', 'xlc', 'xlm', 'xls', 'xlt', 'xlw'))) {
        $subDirectory = 'excel/';
    } elseif (in_array($fileType, array('zip', 'rar', 'z', 'tgz', 'tar'))) {
        $subDirectory = 'compress/';
    } elseif (in_array($fileType, array('exe'))) {
        $subDirectory = 'desktop-app/';
    } else {
        $subDirectory = 'other/';
    }
    return $subDirectory;
}


function findById ($id){
    global $connection;
    global $db_name;
    $sql = "SELECT * FROM `$db_name`.`files` WHERE id=$id;";
    $result = $connection->query($sql);
    if (!$result) {
        return false;
    }
    $row = $result->fetch_assoc();
    return $row;
}