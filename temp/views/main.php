<!DOCTYPE html>
<html>

<head>
    <title>File Upload Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: darkslategrey;
            color: white;
            text-align: center;
            display: flex;
            /* flex-direction: column; */
            height: 100vh;
            align-items: center;
            justify-content: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }

        label {
            margin-top: 20px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            width: 300px;
        }

        input[type="file"] {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            width: 300px;
        }

        #label {
            border: 1px dashed;
            padding: 10px;
            border-radius: 5px;
            width: 10em;
            height: 10em;
            margin-top: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;

        }


        #container {
            /* border: 1px solid; */
            padding: 10px;
            border-radius: 5px;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
        }

        #container.dragover {
            /* background-color: lightblue; */
            border: 3px dashed;
            padding: 10px;
            border-radius: 5px;
            width: 90vw;
            height: 90vh;

        }

        #submit-btn {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: lightblue;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }

        #success,
        #error,
        #fileData {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid;
            color: white;
            font-weight: bold;
            width: 90%;

        }

        #link-container {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid;
            color: white;
            font-weight: bold;
            width: 90%;
            align-items: center;
            justify-content: space-between;
            flex-direction: column;
            gap: 10px;

        }

        #link-container input {
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: lightblue;
            color: black;
            font-weight: bold;
            cursor: pointer;
            width: 90%;
            pointer-events: none;
        }

        #link-container button {
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: lightblue;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }

        #progress {
            width: 300px;
            height: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
            color: lightpink;
        }

        #success-message {
            width: 100%;
            height: 50px;
            background-color: lightblue;
            position: absolute;
            color: black;
            top: 0;
            left: 0;
            display: none;
            align-items: center;
            justify-content: center;

        }
    </style>
</head>

<body>
    <div id="success-message"> </div>
    <div id="container">
        <div id="link-container">
            <input type="text" value="" id="link">
            <button onclick="copyToClipboard()">Copy</button>
        </div>
        <form enctype="multipart/form-data" onsubmit="submitForm(event)">
            <label for="file" id="label" style="cursor: pointer;">
                <div id="progress">Drag and Drop File</div>
                <input type="file" name="file" id="file" hidden onchange="onChange(event)">
            </label>
            <button type="submit" name="submit" id="submit-btn"> Upload File </button>
        </form>
        <div id="fileData">
        </div>
        <div id="success">
        </div>
        <div id="error"></div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const { protocol, host } = window.location;

        async function onChange(event) {
            const file = event.target.files[0];
            const fileData = document.getElementById('fileData');
            fileData.innerHTML = '';
            fileData.style.display = 'block';
            fileData.appendChild(document.createTextNode(file.name));
            fileData.appendChild(document.createElement('br'));
            document.getElementById('submit-btn').style.display = 'block';
            document.getElementById('link-container').style.display = 'none';
            document.getElementById('link').value = '';
            document.getElementById('success').style.display = 'none';
            document.getElementById('success').innerHTML = '';
        }
        async function submitForm(event) {
            event.preventDefault();
            var formData = new FormData(document.forms[0]);
            try {
                const progressElem = document.getElementById('progress');
                const response = await axios.post('<?php echo $process_urls['upload'] ?>', formData, {
                    onUploadProgress: function (progressEvent) {
                        const loaded = progressEvent.loaded;
                        const total = progressEvent.total;
                        if (total) {
                            const percentCompleted = Math.round((loaded * 100) / total);
                            progressElem.innerHTML = `Uploading: ${percentCompleted}%`;
                        }
                    }
                });
                console.log(response)
                const { status, message, data } = response.data;
                const fileData = document.getElementById('fileData');
                fileData.innerHTML = '';
                fileData.style.display = 'none';
                progressElem.innerHTML = 'Drag and Drop File';
                document.getElementById('submit-btn').style.display = 'none';
                const success = document.getElementById('success');
                success.innerHTML = '';
                success.style.display = 'block';
                success.appendChild(document.createTextNode(message));
                success.appendChild(document.createElement('br'));
                success.appendChild(document.createTextNode(data.original_name));
                success.appendChild(document.createElement('br'));
                document.getElementById('link').value = '';
                document.getElementById('file').value = '';
                document.getElementById('link-container').style.display = 'flex';
                document.getElementById('link').value = `${protocol}//${host}${data.view_url}`;
            } catch (error) {
                const errorDiv = document.getElementById('error')
                errorDiv.innerHTML = '';
                errorDiv.style.display = 'block';
                if (error.response) {
                    const errorResponse = error.response.data;
                    errorDiv.appendChild(document.createTextNode(errorResponse.message));
                } else if (error.request) {
                    errorDiv.appendChild(document.createTextNode('No response received'));
                } else {
                    errorDiv.appendChild(document.createTextNode(error.message));
                }
            }
        }
        const container = document.getElementById('container');
        function onDragOver(event) {
            event.preventDefault();
            container.classList.add('dragover');
        }

        // Function to handle dragleave event on the entire window
        function onDragLeave(event) {
            event.preventDefault();
            container.classList.remove('dragover');
        }

        // Function to handle drop event on the entire window
        function onDrop(event) {
            event.preventDefault();
            container.classList.remove('dragover');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const fileInput = document.getElementById('file');
                fileInput.files = files;
                const changeEvent = new Event('change');
                fileInput.dispatchEvent(changeEvent);
            }
        }
        container.addEventListener('dragover', onDragOver);
        container.addEventListener('dragleave', onDragLeave);
        container.addEventListener('drop', onDrop);
        function copyToClipboard() {
            var copyText = document.getElementById("link");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            notify("Copied: " + copyText.value);
        }
        function notify(message) {
            const div = document.getElementById('success-message');
            div.innerHTML = '';
            div.style.display = 'flex';
            div.innerHTML = message;
            setTimeout(() => {
                div.style.display = 'none';
            }, 2000);

        }
    </script>
</body>

</html>