<?php
include_once("../config.php");
$fileId = '';
$type = '';
$error = '';
if (isset($_GET['file'])) {
    $fileId = $_GET['file'];
    $row = findFileById($fileId);
    if (!$row) {
        $error = array(
            "status" => "error",
            "message" => "file Not Found",
        );
    } else {
        $type = $row['type'];
    }
} else {
    $error = array(
        "status" => "error",
        "message" => "file Not Found",
    );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body
style="display: flex;justify-content: center;align-items: center;min-height: 100vh; background-color: #34495E; color: white;"
>
    <?php
    if ($error) {
        echo $error['message'];
    } else {
        if (strpos($type, 'video') !== false) {
            echo '<video id="video1" autoplay controls>';
            echo '<source src="' . $server_url . $process_urls['download'] . '/?file=' . $fileId . '" type="' . $type . '">';
            echo '</video>';
        } elseif (strpos($type, 'image') !== false) {
            echo '<img src="' . $server_url . $process_urls['download'] . '/?file=' . $fileId . '" alt="Image">';
        } else {
            echo '<div>';
            echo '<p>'.'File Id: ' . $fileId.'</p>'. '<br>';
            echo '<p>'.'Name: ' . $row['original_name'] .'</p>'. '<br>';
            echo '<p>'.'Type: ' . $row['type'] .'</p>'. '<br>';
            echo '<p>'.'Size: ' . $row['size'] .'</p>'. '<br>';
            echo '<a style="color: lightcoral; text-decoration: none; border : 1px solid lightcoral; padding: 5px; border-radius: 5px;" href="' . $server_url . $process_urls['download'] . '/?file=' . $fileId . '" download>Download File</a>';
            echo '</div>';
        }
    }
    ?>
</body>

</html>