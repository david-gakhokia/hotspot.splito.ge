# MikroTik RouterOS Hotspot Deployment Script
# გაუშვით RouterOS Terminal-ში

# 1. Hotspot Directory-ის შექმნა
/file
print
cd hotspot

# 2. CSS Directory-ის შექმნა
mkdir css
mkdir js  
mkdir images

# 3. Walled Garden-ის კონფიგურაცია
/ip hotspot walled-garden
add dst-host=towergroup.ge comment="Tower Group Ad Domain"
add dst-host=splito.ge comment="Splito Main Site"
add dst-host=*.google-analytics.com comment="Analytics"
add dst-host=*.googletagmanager.com comment="GTM"
add dst-host=fonts.googleapis.com comment="Google Fonts"

# 4. Hotspot Profile-ის განახლება (თუ საჭიროა)
/ip hotspot profile
set [find name=hsprof1] html-directory=hotspot

# 5. ფაილების ატვირთვა (შეცვალეთ SERVER_URL თქვენი სერვერის მისამართით)
# /tool fetch url="http://YOUR_SERVER/hotspot/login.html" dst-path=hotspot/login.html
# /tool fetch url="http://YOUR_SERVER/hotspot/css/styles.css" dst-path=hotspot/css/styles.css  
# /tool fetch url="http://YOUR_SERVER/hotspot/js/app.js" dst-path=hotspot/js/app.js
# /tool fetch url="http://YOUR_SERVER/hotspot/images/logo.png" dst-path=hotspot/images/logo.png
# /tool fetch url="http://YOUR_SERVER/hotspot/images/tower-group-banner.jpg" dst-path=hotspot/images/tower-group-banner.jpg

# 6. ფაილების სიის შემოწმება
/file
print where name~"hotspot"

# 7. Hotspot Service-ის რესტარტი
/ip hotspot
disable [find]
enable [find]

# 8. Test Login Page
# Browser-ში გახსენით: http://192.168.88.1/login

# ============================================
# PowerShell Script Windows-ზე ლოკალური ტესტირებისთვის
# ============================================

<#
# Local Development Server (PowerShell)
Set-Location "c:\Users\Davit\Herd\hotspot.splito.ge\public"
python -m http.server 8080

# ან Node.js-ით:
# npx http-server -p 8080 -c-1

# შემდეგ მიმართეთ: http://localhost:8080/test-hotspot.html
#>

# ============================================
# ყველაზე მნიშვნელოვანი ფაილები:
# ============================================
# 
# 1. login.html - მთავარი login გვერდი
# 2. css/styles.css - ყველა სტილი 
# 3. js/app.js - JavaScript ფუნქციონალი
# 4. images/ - ლოგო და რეკლამის სურათები
#
# MikroTik RouterOS-ში ეს ფაილები უნდა იყოს:
# Files -> hotspot/ directory-ში
