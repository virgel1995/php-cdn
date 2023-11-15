<?php
if (isset($fileId)) {
    $row = $fileDatabase->findFileById($fileId);
    if (!$row) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(
            array(
                "status" => "error",
                "message" => "File Not Found",
            )
        );
        exit();
    } else {
        $filePath = $targetDirectory . $row['path'];
        if (file_exists($filePath)) {
            $file_type = $row['type'];
            // Send additional information in the response
            $totalSize = filesize($filePath);
            // Set appropriate headers for download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . rawurlencode($row['original_name']) . '"');
            header('Content-Length: ' . $totalSize);
            readfile($filePath);
            $res = $fileDatabase->deleteFileById($fileId, $filePath);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error",
                    "message" => "File Not Found for Download",
                )
            );
        }
    }
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(
        array(
            "status" => "error",
            "message" => "File ID is Required",
        )
    );
}
?>