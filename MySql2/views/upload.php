<?php
if (isset($_FILES['file'])) {
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $subDirectory = process_SubDirectory($fileType);
        $folderType = str_replace('/', '', $subDirectory);

        // Create the target directory if it doesn't exist
        if (!file_exists($targetDirectory . $subDirectory)) {
            mkdir($targetDirectory . $subDirectory, 0777, true);
        }
        // Generate a unique filename using UUIDv4
        $genreated_name = uniqid(prefix: true, more_entropy: true) . '-' . bin2hex(random_bytes(24)) . '.' . $fileType;
        $original_name = $_FILES['file']['name'];
        $uuid = uniqid(prefix: true, more_entropy: true);
        $uniqueFileName = $subDirectory . $genreated_name;
        $targetFile = $targetDirectory . $uniqueFileName;
        $file_size = formatBytes($_FILES['file']['size']);
        $file_type = $_FILES['file']['type'];
        $rows = $fileDatabase->createNewFile($genreated_name, $uuid, $original_name, $uniqueFileName, $file_size, $folderType, $file_type);
        if ($rows) {
            $download_url = $process_urls['main'] . "?file=" . $rows['uuid'] . "&base=false&download=true";
            $base_url = $process_urls['main'] . "?file=" . $rows['uuid'] . "&base=true&download=false";
            $file_url = $process_urls['main'] . "/" . $rows['uuid'] . "&base=true&download=false";
            $view_url = $process_urls['main'] . "/" . $rows['uuid'];
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => "File Uploaded Successfully",
                        "data" => array(
                            "id" => $rows['id'],
                            "name" => $rows['name'],
                            "original_name" => $rows['original_name'],
                            "size" => $rows['size'],
                            'type' => $rows['mime_type'],
                            "download_url" => $download_url,
                            "base_url" => $base_url,
                            'file_url' => $file_url,
                            'view_url' => $view_url
                        ),
                    )
                );
            } else {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        "status" => "error",
                        "message" => "Sorry, there was an error uploading your file."
                    )
                );
            }
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error",
                    "message" => "File upload error: "
                )
            );
        }
    } else {
        if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error.UPLOAD_ERR_INI_SIZE",
                    "message" => "File size exceeds the maximum allowed upload size.
                    The maximum allowed upload size is " . ini_get('upload_max_filesize')
                        . " bytes."

                )
            );
        } elseif ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error.UPLOAD_ERR_FORM_SIZE",
                    "message" => "File size exceeds the maximum allowed upload size."
                )
            );
        } elseif ($_FILES['file']['error'] == UPLOAD_ERR_PARTIAL) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error.UPLOAD_ERR_PARTIAL",
                    "message" => "File upload error: " . $_FILES['file']['error'],
                )
            );
        } elseif ($_FILES['file']['error'] == UPLOAD_ERR_NO_TMP_DIR) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error.UPLOAD_ERR_NO_TMP_DIR",
                    "message" => "File upload error: " . $_FILES['file']['error'],
                )
            );
        } elseif ($_FILES['file']['error'] == UPLOAD_ERR_CANT_WRITE) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error tstst",
                    "message" => "File upload error: " . $_FILES['file']['error'],
                )
            );
        } elseif ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error.UPLOAD_ERR_NO_FILE",
                    "message" => "There Are No File To Upload",
                )
            );
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status" => "error",
                    "message" => "File upload error: " . $_FILES['file']['error'],
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
            "message" => "File key not found in the request."
        )
    );
}
