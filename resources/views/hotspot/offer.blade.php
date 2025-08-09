<!DOCTYPE html>
<html lang="ka">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Free Wi‑Fi Offer</title>
    <style>
        :root {
            --blue: #2563eb;
            --blue-hover: #1d4ed8;
            --green: #10b981;
            --green-hover: #059669;
            --card-shadow: 0 6px 18px rgba(0, 0, 0, .12);
            --radius: 14px;
        }

        * {
            box-sizing: border-box
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            color: #111827;
            background: #f7f7fb;
        }

        .title-section {
            text-align: center;
            padding: 28px 16px 12px;
        }

        .title-section h1 {
            margin: 0;
            font-size: clamp(22px, 3vw, 32px);
            font-weight: 800;
            letter-spacing: .2px;
        }

        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 16px 22px;
        }

        .card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .media {
            display: block;
            width: 100%;
            height: 540px;
            /* Increased height */
            object-fit: cover;
        }

        .card-body {
            padding: 18px 18px 6px;
            text-align: center;
            color: #374151;
            line-height: 1.45;
            font-size: clamp(14px, 1.8vw, 16px);
        }

        .footer-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            align-items: center;
            padding: 18px 16px 26px;
            flex-wrap: nowrap;
        }

        .btn {
            appearance: none;
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .05s ease, box-shadow .2s ease, background-color .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-info {
            background: var(--blue);
            color: #fff;
        }

        .btn-info:hover {
            background: var(--blue-hover);
        }

        .btn-connect {
            background: var(--green);
            color: #04230f;
        }

        .btn-connect:hover {
            background: var(--green-hover);
            color: #fff;
        }

        @media (max-width:460px) {
            .footer-buttons {
                flex-wrap: wrap;
            }

            .btn {
                min-width: unset;
                width: 100%;
            }
        }
    </style>
    <script>
        // Trial login function to connect users to the internet via hotspot
        function connectWifi() {
            // ამ მისამართში ჩაწერე შენი Hotspot-ის IP და სასურველი გადასვლის გვერდი
            var routerIP = "192.168.100.1";
            var dst = encodeURIComponent("https://google.com"); // ინტერნეტში გადასვლის შემდეგ სად წავიდეს
            window.location.href = "http://" + routerIP + "/login?username=&password=&dst=" + dst;
        }
    </script>
</head>

<body>
    <div class="title-section">
        {{-- <h1>Apartments in Batumi for investment</h1> --}}
    </div>

    <div class="wrap">
        <div class="card">
            <img class="media" src="https://hotspot.splito.ge/hotspot/images/mardi_ad.jpg"
                alt="Apartments in Batumi for investment">
            <div class="card-body">
                <p>The first All-Inclusive hotel in Georgia, offering a wide range of health-enhancing procedures, daily
                    bus tours to hot springs, and a private beach</p>
            </div>
            <div class="footer-buttons">
                <a class="btn btn-info" href="https://invest.mardi.ge/offer" target="_blank" rel="noopener">About
                    More</a>
                <button class="btn btn-connect" onclick="connectWifi()">Connect WiFi</button>
            </div>
        </div>
    </div>
</body>

</html>
