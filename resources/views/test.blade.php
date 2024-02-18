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
    <button type="submit" id="sendButton" onclick="sendRequest()">
        <span class="spinner" id="spinner"></span>
        <p id="text">Send Request</p>
    </button>

    <script type="text/javascript">
        const sendButton = document.getElementById('sendButton');
        const spinner = document.getElementById('spinner');
        const text = document.getElementById('text');

        async function sendRequest() {
            try {
                // Disable the button and show the spinner during the request
                sendButton.disabled = true;
                spinner.style.display = 'inline-block';
                text.style.display = 'none'
                const url = 'https://test-iamservices.semati.sa/nafath/api/v1/client/authorize/';
                const apiKey = 'apikey 21c0f19a-fce5-4d4d-b30f-ffd1c3860731';

                const data = {
                    id: '24324323334',
                    action: 'SpRequest',
                    service: 'DigitalServiceEnrollmentWithoutBio'
                };

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Access-Control-Allow-Origin': '*',
                        'Access-Control-Allow-Methods': 'DELETE, POST, GET, OPTIONS',
                        'Content-Type': 'application/json',
                        'Authorization': apiKey,
                        'Access-Control-Allow-Headers': 'Content-Type, Authorization, X-Requested-With'
                    },
                    body: JSON.stringify(data)
                });
                console.log(response);

                const responseData = await response.json();
                console.log(responseData);

            } catch (error) {
                console.error('Error:', error);

            } finally {
                // Re-enable the button and hide the spinner after the request
                sendButton.disabled = false;
                spinner.style.display = 'none';
                text.style.display = 'block';
            }
        }
    </script>
</body>

</html>