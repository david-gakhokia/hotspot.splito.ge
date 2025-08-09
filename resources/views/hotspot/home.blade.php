<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hotspot Offer Test Page</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        iframe {
            flex: 1 1 auto;
            border: none;
        }
        .controls {
            flex: 0 0 auto;
            padding: 1rem;
            background: #f7f7f7;
            text-align: center;
        }
        button {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c9302c;
        }
    </style>
    <script>
        // Trigger trial login to the Mikrotik hotspot. Adjust URL as needed.
        function connectInternet() {
            // Replace with your router's hotspot IP and desired redirect
            var hotspotLoginUrl = 'http://192.168.100.1/login?username=&password=&dst=https://google.com';
            var img = new Image();
            img.src = hotspotLoginUrl;
            // After a short delay, forward user to the chosen landing page
            setTimeout(function() {
                window.location.href = 'https://google.com';
            }, 3000);
        }
    </script>
</head>
<body>
    <!-- Display the original offer page inside an iframe. Note that the remote site must allow framing -->
    <iframe src="https://invest.mardi.ge/offer" title="Offer"></iframe>
    <div class="controls">
        <button onclick="connectInternet()">გაუქმება / ინტერნეტის ჩართვა</button>
    </div>
</body>
</html>
