<?php
include("../config.php");

if (isset($_FILES['file'])) {
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
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
        } elseif (in_array($fileType, array('pdf'))) {
            $subDirectory = 'pdf/';
            $folderType = 'pdf';
        } elseif (in_array($fileType, array('docx', 'doc'))) {
            $subDirectory = 'word/';
            $folderType = 'word';
        } elseif (in_array($fileType, array('xla', 'xlsx', 'xlc', 'xlm', 'xls', 'xlt', 'xlw'))) {
            $subDirectory = 'excel/';
            $folderType = 'excel';
        } elseif (in_array($fileType, array('zip', 'rar', 'z', 'tgz', 'tar'))) {
            $subDirectory = 'compress/';
            $folderType = 'compress';
        } elseif (in_array($fileType, array('exe'))) {
            $subDirectory = 'desktop-app/';
            $folderType = 'desktop-app';
        } else {
            $subDirectory = 'other/';
            $folderType = 'other';
        }

        if (!file_exists($targetDirectory . $subDirectory)) {
            mkdir($targetDirectory . $subDirectory, 0777, true);
        }

        $generated_name = uniqid(prefix: true, more_entropy: true) . '-' . bin2hex(random_bytes(24)) . '.' . $fileType;
        $uniqueFileName = $subDirectory . $generated_name;
        $targetFile = $targetDirectory . $uniqueFileName;
        $file_size = formatBytes($_FILES['file']['size']);
        $file_type = $_FILES['file']['type'];
        $sql = "INSERT INTO \"$db_name\".\"files\"
        (
            \"name\",
            \"path\",
            \"size\",
            \"type\",
            \"mime_type\"
        ) 
        VALUES (
            '{$generated_name}',       -- name
        '{$uniqueFileName}',       -- path
        '{$file_size}',            -- size
        '{$folderType}',           -- type
        '{$file_type}'             -- mime_type
        ) RETURNING *";

        $inserting = pg_query($connection, $sql);
        if (!$inserting) {
            die("Insert query failed: " . pg_last_error($connection));
        }
        $row = pg_fetch_assoc($inserting);
        $download_url = $process_urls['download'] . "?file=" . $row['id'] . "&base=false&download=true";
        $base_url = $process_urls['download'] . "?file=" . $row['id'] . "&base=true&download=false";
        $file_url = $process_urls['download'] . "?file=" . $row['id'] . "&base=true&download=false";
        $view_url = $process_urls['view'] . "?file=" . $row['id'];
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array(
                "status" => "success",
                "message" => "File Uploaded Successfully",
                "data" => array(
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "size" => $row['size'],
                    'type' => $row['mime_type'],
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
