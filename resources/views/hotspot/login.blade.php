<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Free WiFi Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background: #f4f4f4;
        }

        .wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .banner {
            width: 100%;
            display: block;
        }

        .overlay-text {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.8);
        }

        .form-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-connect {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-connect:hover {
            background-color: #0056b3;
        }

        .error-msg {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <img src="{{ asset('hotspot/images/mardi_ad.jpg') }}" alt="Banner" class="banner">
        <div class="overlay-text">Connect to Free WiFi</div>
    </div>

    <div class="form-container">
        @if (isset($params['link-login']))
        <form method="POST" action="{{ $params['link-login'] }}">
            <input type="hidden" name="username" value="freeuser">
            <input type="hidden" name="password" value="123">
            <input type="hidden" name="link-orig" value="{{ $params['link-orig'] ?? '' }}">
            <button type="submit" class="btn-connect">Connect</button>
        </form>
        @else
            <p class="error-msg">Router login URL not provided. Please try again.</p>
        @endif
    </div>

</body>
</html>
