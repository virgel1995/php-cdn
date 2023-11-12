<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
$server_url = "http://localhost";
$process_urls = array(
    'download' => '/cdn/pg/download',
    'view' => '/cdn/pg/view',
    'upload' => '/cdn/pg/upload/index.php',
);
$targetDirectory = "../uploads/";
$allowedBase64 = array('images', 'pdf');
$mediaFiles = array('audio', 'video');


$db_host = "localhost"; // Change to your database host if necessary
$db_user = "postgres";
$db_port = '5432';
$db_pass = "masterkey";
$db_name = "cdn";

$connection = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$connection) {
    die("Connection failed: " . pg_last_error());
}
$createSchemaQuery = "CREATE SCHEMA IF NOT EXISTS cdn;";
$result = pg_query($connection, $createSchemaQuery);

if (!$result) {
    die("Query failed: " . pg_last_error($connection));
}
$sql = "CREATE TABLE IF NOT EXISTS \"$db_name\".\"files\" 
(
    \"id\" SERIAL PRIMARY KEY,
    \"name\" VARCHAR(255) NULL, 
    \"original_name\" VARCHAR(255) NULL, 
    \"path\" VARCHAR(255) NOT NULL,
    \"size\" VARCHAR(255) NOT NULL, 
    \"type\" VARCHAR(255) NOT NULL,
    \"mime_type\" VARCHAR(255) NOT NULL
)";

// Execute the query using pg_query()
$result = pg_query($connection, $sql);
if (!$result) {
    die("Query failed: " . pg_last_error($connection));
}


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