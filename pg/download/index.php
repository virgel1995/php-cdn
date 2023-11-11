<?php
include_once("../config.php");
$base = false;
$download = false;
if (isset($_GET['base'])) {
    $base = $_GET['base'];
}
if (isset($_GET['download'])) {
    $download = $_GET['download'];
}
if (isset($_GET['file'])) {
    $fileId = $_GET['file'];
    $sql = "SELECT * FROM \"$db_name\".\"files\" WHERE id=$fileId;";
    $result = pg_query($connection, $sql);

    if ($result) {
        $row = pg_fetch_assoc($result);
        $filePath = $targetDirectory . $row['path'];
        if (file_exists($filePath)) {
            $file_type = $row['type'];
            if (in_array($file_type, $allowedBase64)) {
                if ($base === "true" && $download !== 'true') {
                    $img = file_get_contents($filePath);
                    $file = base64_encode($img);
                    $img_type = mime_content_type($filePath);
                    $file_size = filesize($filePath);
                    $formatedSize = formatBytes($file_size);
                    http_response_code(200);
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        "status" => "success",
                        "message" => "File fetched Successfully",
                        "data" => array(
                            "size" => $formatedSize,
                            "mime_type" => $img_type,
                            "type" => $file_type,
                            "file" => 'data:' . $img_type . ';base64,' . $file,
                        )
                    ));
                } elseif ($download === 'true') {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                } else {
                    header("Cache-Control: public, max-age=3600");
                    header("Content-Type: " . mime_content_type($filePath));
                    readfile($filePath, false);
                }
            }
            elseif (in_array($file_type, $mediaFiles)) {
                header("Cache-Control: no-cache");
                header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
            }
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(array(
                "status" => "error",
                "message" => "File Not Found",
            ));
        }
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(array(
            "status" => "error",
            "message" => "file Not Found",
        ));
    }
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(array(
        "status" => "error",
        "message" => "file Query is Required",
    ));
}
