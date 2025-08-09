<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Free Wi‑Fi Offer</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #333;
        }
        .hero {
            /* hero background using the Mardi advertisement image provided by the user */
            /* The image is hosted externally at hotspot.splito.ge */
            background-image: url('https://hotspot.splito.ge/hotspot/images/mardi_ad.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 20px;
            color: #fff;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            line-height: 1.4;
        }
        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        a.btn, button.btn {
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        a.btn-info {
            background-color: #0275d8;
            color: #fff;
        }
        a.btn-info:hover {
            background-color: #025aa5;
        }
        button.btn-cancel {
            background-color: #d9534f;
            color: #fff;
        }
        button.btn-cancel:hover {
            background-color: #c12e2a;
        }
    </style>
    <script>
        // Trial login function to connect users to the internet via hotspot
        function connectInternet() {
            var hotspotLoginUrl = 'http://192.168.100.1/login?username=&password=&dst=https://google.com';
            var img = new Image();
            img.src = hotspotLoginUrl;
            setTimeout(function() {
                window.location.href = 'https://google.com';
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="hero">
        <div class="overlay"></div>
        <div class="content">
            <h1>Apartments in Batumi for investment</h1>
            <!-- <p>აპარტამენტები 75 000 დოლარიდან და 15% წლიური შემოსავალი.<br />
            პირველმა All‑Inclusive სასტუმრომ საქართველოში, თბილი ზღვისპირა კლიმატი, უამრავი აქტივობა და კერძო სანაპირო.</p> -->
            <div class="buttons">
                <a href="https://invest.mardi.ge/offer" target="_blank" class="btn btn-info">დეტალური ინფორმაცია</a>
                <button class="btn btn-cancel" onclick="connectInternet()">გაუქმება / ინტერნეტის ჩართვა</button>
            </div>
        </div>
    </div>
</body>
</html>
