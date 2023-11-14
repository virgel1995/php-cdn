<?php
$request_uri = $_SERVER['REQUEST_URI'];
set_time_limit(0);
ini_set('memory_limit', '2048M');
$process_urls = array(
    'main' => '/cdn/temp/',
    'download' => '/cdn/temp/download',
    'view' => '/cdn/temp/:slug',
    'upload' => '/cdn/temp/upload',
);
// main directory
$targetDirectory = "./uploads/";
// sub directorys
$allowedBase64 = array('images', 'pdf');
$mediaFiles = array('audio', 'video');
$applications = array('desktop-app', 'word', 'excel', 'pdf', 'compress', 'other');

/**
 * Converts a given number of bytes into a human-readable format.
 *
 * @param int $bytes The number of bytes to be converted.
 * @param int $precision The number of decimal places to round the result to. Default is 2.
 * @return string The formatted string representing the converted value in the appropriate unit.
 */
function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . $units[$pow];
}

/**
 * Returns the appropriate subdirectory based on the file type.
 *
 * @param string $fileType The file type to process.
 * @return string The subdirectory corresponding to the file type.
 */
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

