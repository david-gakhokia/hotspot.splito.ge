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
            background: linear-gradient(90deg, #3a8dde 0%, #0056b3 100%);
            color: #fff;
            border: none;
            padding: 16px 36px;
            font-size: 20px;
            border-radius: 32px;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(0, 86, 179, 0.18), 0 1.5px 4px rgba(0,0,0,0.08);
            font-weight: 600;
            letter-spacing: 1.2px;
            transition: background 0.25s, transform 0.15s, box-shadow 0.2s;
            outline: none;
        }

        .btn-connect:hover, .btn-connect:focus {
            background: linear-gradient(90deg, #0056b3 0%, #3a8dde 100%);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 24px rgba(0, 86, 179, 0.22), 0 2px 8px rgba(0,0,0,0.10);
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
        <button class="overlay-text btn-connect" style="backdrop-filter: blur(2px);" onclick="document.getElementById('hotspot-login-form')?.scrollIntoView({behavior:'smooth'});">
            <span style="display:inline-flex;align-items:center;gap:10px;">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;"><circle cx="11" cy="11" r="10" stroke="#fff" stroke-width="2" fill="none"/><path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Connect Internet
            </span>
        </button>
    </div>

    <div class="form-container" id="hotspot-login-form">
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
