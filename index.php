<!DOCTYPE html>
<html>
<head>
    <title>File Upload Form</title>
</head>
<body>
    <form action="/cdn/upload/index.php" method="post" enctype="multipart/form-data">
        <label for="file">Choose a file to upload:</label>
        <input type="file" name="file" id="file">
        <input type="submit" name="submit" value="Upload File">
    </form>
    <iframe id="hidden-iframe" name="hidden-iframe" style="display: none;"></iframe>
</body>
</html>
