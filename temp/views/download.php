<?php
var_dump($fileId);
if (isset($fileId)) {
    $row = $fileDatabase->findFileById($fileId);
    if (!$row) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(
            array(
                "status" => "error",
                "message" => "file Not Found",
            )
        );
        exit();
    } else {
        $filePath = $targetDirectory . $row['path'];
        if (file_exists($filePath)) {
            $file_type = $row['type'];
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $row['original_name'] . '"');
            header('Content-Length: ' . filesize($filePath));
            $file = fopen($filePath, "rb");
            while (!feof($file)) {
                print(fread($file, 1024 * 8));
                ob_flush();
                flush();
            }
            fclose($file);
            $res = $fileDatabase->deleteFileById($fileId, $filePath);
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => "File Downloaded Successfully",
                    "data" => $res
                )
            );

        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error",
                    "message" => "File Not Found Download",
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
            "message" => "file Query is Required",
        )
    );
}
