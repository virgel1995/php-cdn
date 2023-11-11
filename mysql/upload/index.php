<?php
include_once("../config.php");

if (isset($_FILES['file'])) {
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) { // Check for upload error
        // Determine the file type
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $subDirectory = '';
        $folderType = '';
        // Check the file type and assign a subdirectory accordingly
        if (in_array($fileType, array('jpg', 'jpeg', 'png', 'gif'))) {
            $subDirectory = 'images/';
            $folderType = 'images';
        } elseif (in_array($fileType, array('mp4', 'avi', 'mkv'))) {
            $subDirectory = 'video/';
            $folderType = 'video';
        } elseif (in_array($fileType, array('mp3', 'wav'))) {
            $subDirectory = 'audio/';
            $folderType = 'audio';
        } 
        elseif (in_array($fileType, array('pdf'))) {
            $subDirectory = 'pdf/';
            $folderType = 'pdf';
        }
        elseif (in_array($fileType, array('docx' , 'doc'))) {
            $subDirectory = 'word/';
            $folderType = 'word';
        }
         elseif (in_array($fileType, array('xla','xlsx', 'xlc', 'xlm', 'xls', 'xlt', 'xlw'))) {
            $subDirectory = 'excel/';
            $folderType = 'excel';
        } 
         elseif (in_array($fileType, array('zip', 'rar', 'z', 'tgz', 'tar'))) {
            $subDirectory = 'compress/';
            $folderType = 'compress';
        } 
         elseif (in_array($fileType, array('exe'))) {
            $subDirectory = 'desktop-app/';
            $folderType = 'desktop-app';
        } 
        else {
            $subDirectory = 'other/';
            $folderType = 'other';
        }

        // Create the target directory if it doesn't exist
        if (!file_exists($targetDirectory . $subDirectory)) {
            mkdir($targetDirectory . $subDirectory, 0777, true);
        }

        // Generate a unique filename using UUIDv4
        $genreated_name =  uniqid(prefix: true, more_entropy: true) . '-' . bin2hex(random_bytes(24)) . '.' . $fileType;
        $uniqueFileName = $subDirectory . $genreated_name;
        $targetFile = $targetDirectory . $uniqueFileName;
        $file_size = formatBytes($_FILES['file']['size']);
        $file_type = $_FILES['file']['type'];

        $sql = "INSERT INTO `files`
        (
            `name`,
            `path`,
            `size`,
            `type`,
            `mime_type`
            ) 
        VALUES (
            '$genreated_name',       #name
            '$uniqueFileName',       #path
            '$file_size',            #size
            '$folderType',           #type
            '$file_type'             #mime_type
            );";
        // get the file insrted into database
        $last_sql = "SELECT * FROM `files` WHERE id = LAST_INSERT_ID()";
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
