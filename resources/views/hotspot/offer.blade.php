<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Internet hotspot - Log in</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- two other colors
  <body class="lite">
  <body class="dark">
  -->

    $(if chap-id)
    <form name="sendin" action="$(link-login-only)" method="post" style="display:none">
        <input type="hidden" name="username" />
        <input type="hidden" name="password" />
        <input type="hidden" name="dst" value="$(link-orig)" />
        <input type="hidden" name="popup" value="true" />
    </form>

    <script src="/md5.js"></script>
    <script>
        function doLogin() {
            document.sendin.username.value = document.login.username.value;
            document.sendin.password.value = hexMD5('$(chap-id)' + document.login.password.value + '$(chap-challenge)');
            document.sendin.submit();
            return false;
        }
    </script>
    $(endif)

    <div class="ie-fixMinHeight">
        <div class="main">
            <div class="wrap animated fadeIn">
                <form name="login" action="$(link-login-only)" method="post" $(if chap-id)
                    onSubmit="return doLogin()" $(endif)>
                    <input type="hidden" name="dst" value="$(link-orig)" />
                    <input type="hidden" name="popup" value="true" />

                    <!-- Image Card -->
                    <div style="
 max-width: 95%;
 margin: 0 auto;
 border-radius: 20px;
 overflow: hidden;
 box-shadow: 0 8px 25px rgba(0,0,0,0.15);
 cursor: pointer;
 "
                        onclick="openInvestmentLink()">
                        <img src="{{ asset('img/mardi_ad.jpg') }}" alt="Logo"
                            style="
   width: 100%;
   height: auto;
   display: block;
   transition: transform 0.3s ease;
 "
                            onmouseover="this.style.transform='scale(1.02)'"
                            onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <!-- Error-only message (trial ტექსტი ქვემოთ ღილაკად გადავიტანეთ) -->
                    <p class="info $(if error)alert$(endif)">
                        $(if error)$(error)$(endif)
                    </p>

                    <!-- About Offer Button -->
                    <div style="text-align: center; margin-top: 20px;">
                        <button type="button" onclick="openInvestmentLink()"
                            style="
              background: linear-gradient(135deg, #63b765 0%, #409242 100%);
              color: white;
              border: none;
              padding: 12px 24px;
              border-radius: 25px;
              font-size: 16px;
              font-weight: 600;
              cursor: pointer;
              box-shadow: 0 4px 15px rgba(0,0,0,0.2);
              transition: all 0.3s ease;
              text-transform: uppercase;
              letter-spacing: 1px;">
                            About Offer
                        </button>
                    </div>

                    <!-- Connect Free WiFi Button (მხოლოდ trial ითვის) -->
                    $(if trial == 'yes')
                    <div style="text-align: center; margin-top: 12px;">
                        <a href="$(link-login-only)?dst=$(link-orig-esc)&amp;username=T-$(mac-esc)"
                            style="
                 display: inline-block;
                 background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
                 color: #fff; text-decoration: none;
                 padding: 12px 24px;
                 border-radius: 25px;
                 font-size: 16px; font-weight: 700;
                 letter-spacing: .5px; text-transform: uppercase;
                 box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                 transition: transform .2s ease, box-shadow .2s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'">
                            Connect Free WiFi
                        </a>
                    </div>
                    $(endif)

                    <script>
                        function openInvestmentLink() {
                            window.open(
                                'https://invest.mardi.ge/offer?utm_source=wifi&utm_medium=awh-offer&utm_campaign={campaignid}&utm_content={adgroupid}&utm_term={keyword}',
                                '_blank');
                        }
                    </script>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
