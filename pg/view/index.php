<?php
$fileId = '';
$error = '';

if (isset($_GET['file'])) {
    $fileId = $_GET['file'];
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

<body>
    <?php
    if ($error) {
        echo $error['message'];
    }
    ?>
    <video style="display: <?php if ($error) {
        echo 'none';
                            } else {
                                echo '';
                            }
                            ?>;" id="video1" autoplay controls>
        <source src="http://localhost/cdn/download/?file=<?php echo $fileId ?>" type="video/mp4">
    </video>
</body>

</html>