<?php
include_once("../config.php");

if (isset($_FILES['file'])) {
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) { // Check for upload error
        // Determine the file type
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $subDirectory = process_SubDirectory($fileType);
        $folderType = str_replace('/', '', $subDirectory);

        // Create the target directory if it doesn't exist
        if (!file_exists($targetDirectory . $subDirectory)) {
            mkdir($targetDirectory . $subDirectory, 0777, true);
        }

        // Generate a unique filename using UUIDv4
        $genreated_name =  uniqid(prefix: true, more_entropy: true) . '-' . bin2hex(random_bytes(24)) . '.' . $fileType;
        $original_name = $_FILES['file']['name'];
        $uniqueFileName = $subDirectory . $genreated_name;
        $targetFile = $targetDirectory . $uniqueFileName;
        $file_size = formatBytes($_FILES['file']['size']);
        $file_type = $_FILES['file']['type'];

        $sql = "INSERT INTO `$db_name`.`files`
        (
            `name`,
            `original_name`,
            `path`,
            `size`,
            `type`,
            `mime_type`
            ) 
        VALUES (
            '$genreated_name',       #name
            '$original_name',       #name
            '$uniqueFileName',       #path
            '$file_size',            #size
            '$folderType',           #type
            '$file_type'             #mime_type
            );";
        // get the file insrted into database
        $last_sql = "SELECT * FROM `$db_name`.`files` WHERE id = LAST_INSERT_ID()";
        $insirting = $connection->query($sql);
        $result = $connection->query($last_sql);
        $rows = $result->fetch_assoc();
        if ($rows) {
            $download_url = $process_urls['download'] . "?file=" . $rows['id'] . "&base=false&download=true";
            $base_url = $process_urls['download'] . "?file=" . $rows['id'] . "&base=true&download=false";
            $file_url = $process_urls['download'] . "?file=" . $rows['id'] . "&base=true&download=false";
            $view_url = $process_urls['view'] . "?file=" . $rows['id'];
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array(
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
                ));
            } else {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Sorry, there was an error uploading your file."
                ));
            }
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(array(
                "status" => "error",
                "message" => "File upload error: " . $connection->connect_error
            ));
        }
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            "status" => "error",
            "message" => "File upload error: " . $_FILES['file']['error']
        ));
    }
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(array(
        "status" => "error",
        "message" => "File key not found in the request."
    ));
}
$connection->close();
