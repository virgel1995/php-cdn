<?php
$title = $APP_NAME;
include __DIR__ . '/partails/head.php';
?>

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
                const { status, message, data } = response.data;
                console.log({
                    response
                });

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
                console.log({ error });
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
        if (container) {
            container.addEventListener('dragover', onDragOver);
            container.addEventListener('dragleave', onDragLeave);
            container.addEventListener('drop', onDrop);
        }
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