<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');
$server_url = "http://localhost";
$process_urls = array(
    'download' => '/cdn/sqlite/download',
    'view' => '/cdn/sqlite/view',
    'upload' => '/cdn/sqlite/upload/index.php',
);
// main directory
$targetDirectory = "../uploads/";
// sub directorys
$allowedBase64 = array('images', 'pdf');
$mediaFiles = array('audio', 'video');
$applications = array('desktop-app', 'word', 'excel', 'pdf', 'compress', 'other');

$db_name = "../database/cdn.db";

$connection = new SQLite3($db_name);

if (!$connection) {
    die("Connection failed: " . $connection->lastErrorMsg());
}
$sql = "CREATE TABLE IF NOT EXISTS `files` (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NULL,
    original_name TEXT NULL,
    path TEXT NOT NULL,
    size TEXT NOT NULL,
    type TEXT NOT NULL,
    mime_type TEXT NOT NULL
)";
$result = $connection->exec($sql);
if (!$result) {
    die("Table creation failed: " . $connection->lastErrorMsg());
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


function findFileById($id)
{
    global $connection;
    $sql = "SELECT * FROM `files` WHERE id=$id;";
    $result = $connection->query($sql);
    if (!$result) {
        return false;
    }
    $row = $result->fetchArray(SQLITE3_ASSOC);
    return $row;
}
function createNewFile ($genreated_name, $original_name, $uniqueFileName, $file_size, $folderType, $file_type) {
    global $connection;
    $sql = "INSERT INTO `files`
    (
        `name`,
        `original_name`,
        `path`,
        `size`,
        `type`,
        `mime_type`
    ) 
    VALUES (
        '$genreated_name',
        '$original_name',
        '$uniqueFileName',
        '$file_size',
        '$folderType',
        '$file_type'
    )";  
    $result = $connection->exec($sql);
    if (!$result) {
        die("Query failed: " . $connection->lastErrorMsg());
    }
    $lastInsertID = $connection->lastInsertRowID();
    
    $last_sql = "SELECT * FROM `files` WHERE id = $lastInsertID";
    $result = $connection->query($last_sql);
    if (!$result) {
        die("Query failed: " . $connection->lastErrorMsg());
    }
    $rows = $result->fetchArray(SQLITE3_ASSOC);
    return $rows;
}