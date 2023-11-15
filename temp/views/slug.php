<?php
$fileId = '';
$type = '';
$error = '';
if (isset($slug)) {
    $fileId = $slug;
    $row = $fileDatabase->findFileById($fileId);
    if ($row === false) {
        $error = array(
            "status" => "error",
            "message" => "File Not Found ",
            "code" => "404",
        );
    } else {
        $type = $row['type'];
        $filePath = $targetDirectory . $row['path'];
        if (!file_exists($filePath)) {
            $error = array(
                "status" => "error",
                "message" => "No File Found",
            );
        }
    }
} else {
    $error = array(
        "status" => "error",
        "message" => "File Not Found No Query",
    );
}
$title = '';
if ($error) {
    $title = $error['code'] . ' - ' . $error['message'];
} else {
    $title = $APP_NAME . ' - ' . $row['original_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $process_urls['main'] ?>/assets/style.css">
    <title>
        <?php echo $title ?>
    </title>
</head>
<body>
    <?php
    $serverUrl = $process_urls['download'] . '/' . $fileId;
    if (!$error) {
        echo '<div id ="fileData-slug">';
        echo '<p>' . 'Name: ' . $row['original_name'] . '</p>' . '<br>';
        echo '<p>' . 'Size: ' . $row['size'] . '</p>' . '<br>';
        echo '<button id="download-btn" onclick="handleDownload()">Download</button>';
        echo '<div id="progress" style="margin-top: 10px;"></div>';
        echo '</div>';
    } else {
        echo $error['code'] . ' - ' . $error['message'];
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const { protocol, host } = window.location;
        const serverUrl = protocol + '//' + host + '<?php echo $serverUrl; ?>';
        const handleDownload = () => {
            const progressElem = document.getElementById('download-btn');
            progressElem.disabled = true
            progressElem.style.cursor = 'not-allowed';
            axios({
                url: serverUrl,
                method: 'POST',
                responseType: 'blob',
                onDownloadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    progressElem.innerHTML = `Downloading: ${percentCompleted}%`;
                },
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `<?php echo $row['original_name']; ?>`);
                document.body.appendChild(link);
                link.click();
                progressElem.innerHTML = 'Download';
                progressElem.disabled = false
                progressElem.style.cursor = 'pointer';
                document.body.removeChild(link);
                document.getElementById('fileData-slug').innerHTML = 'your download finished';

            }).catch((error) => {
                progressElem.innerHTML = 'Download failed';
            });
        }
    </script>
</body>

</html>