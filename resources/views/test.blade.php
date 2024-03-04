<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Request</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .spinner {
            display: none;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left: 4px solid #4CAF50;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <button type="submit" id="sendButton">
        <span class="spinner" id="spinner"></span>
        <p id="text">Send Request</p>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script type="module">
        const sendButton = document.getElementById('sendButton');

        sendButton.addEventListener('click', async function () {
            // Disable the button and show the spinner
            sendButton.disabled = true;
            spinner.style.display = 'inline-block';
            text.style.display = 'none';

            try {
                // Make a POST request using Axios
                const response = await axios.post('/test');

                // Handle the response data here
                console.log('Response:', response.data);
            } catch (error) {
                console.error('Error:', error);
            } finally {
                // Re-enable the button and hide the spinner after the request
                sendButton.disabled = false;
                spinner.style.display = 'none';
                text.style.display = 'block';
            }
        });
    </script>
</body>

</html>
