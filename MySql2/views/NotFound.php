<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Page Not Found </title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #34495E;
            color: white;
        }
    </style>
</head>

<body>
    <div> 404 Not Found </div>
    <div> Page Not Found </div>
    <script>
        console.log('<?php echo $requestPath; ?>')
    </script>
</body>

</html>