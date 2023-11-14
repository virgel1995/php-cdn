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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        if ($error) {
            echo $error['code'] . ' - ' . $error['message'];
        } else {
            echo $row['original_name'];
        }
        ?>
    </title>
    <style>
        #download-btn {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: lightblue;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body
    style="display: flex;justify-content: center;align-items: center; flex-direction: column;min-height: 100vh; background-color: #34495E; color: white;">
    <?php
    $serverUrl = $process_urls['download'] . '/' . $fileId;
    if (!$error) {
        echo '<div id="fileData">';
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
                console.log(response.data);
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', '<?php echo $row['original_name']; ?>');
                document.body.appendChild(link);
                link.click();
                progressElem.innerHTML = 'Download';
                progressElem.disabled = false
                progressElem.style.cursor = 'pointer';
                document.body.removeChild(link);
                document.getElementById('fileData').innerHTML = 'your download finished';

            }).catch((error) => {
                console.log(error);
                progressElem.innerHTML = 'Download failed';
            });
        }
    </script>
</body>

</html>