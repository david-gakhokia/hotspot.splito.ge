# MikroTik RouterOS Hotspot Configuration Script
# ჩაწერეთ ეს команდები RouterOS Terminal-ში ან WinBox-ის Terminal-ში

# ==========================================
# 1. HTML Directory Configuration
# ==========================================

# hotspot profile-ში html directory-ის დაყენება
/ip hotspot profile
set [find name=hsprof1] html-directory=hotspot

# თუ სხვა profile გაქვთ, შეცვალეთ hsprof1 თქვენი profile-ის სახელით
# ყველა profile-ის სახელის სანახავად:
# /ip hotspot profile print

# ==========================================
# 2. Walled Garden Configuration  
# ==========================================

# რეკლამისა და საიტების დომეინების დამატება
/ip hotspot walled-garden
add dst-host=towergroup.ge comment="Tower Group Advertisement"
add dst-host=splito.ge comment="Splito Main Website" 
add dst-host=*.google-analytics.com comment="Google Analytics"
add dst-host=*.googletagmanager.com comment="Google Tag Manager"
add dst-host=fonts.googleapis.com comment="Google Fonts"
add dst-host=fonts.gstatic.com comment="Google Fonts Static"

# ==========================================
# 3. File Directory Creation
# ==========================================

# ტერმინალში file სექციაში directory-ების შექმნა
/file
print
cd ..
mkdir hotspot
cd hotspot
mkdir css
mkdir js
mkdir images

# ==========================================
# 4. Files Upload (via FTP or SCP)
# ==========================================

# Option A: FTP Upload (PowerShell script-ით)
# გაუშვით: .\mikrotik-upload.ps1

# Option B: Manual Upload via WinBox
# 1. WinBox -> Files
# 2. ატვირთეთ ფაილები შემდეგ სტრუქტურაში:
#    hotspot/
#    ├── login.html
#    ├── css/styles.css
#    ├── js/app.js
#    └── images/
#        ├── logo.png
#        └── tower-group-banner.jpg

# ==========================================
# 5. Service Restart
# ==========================================

# Hotspot service-ის რესტარტი ახალი ფაილების ასამუშავებლად
/ip hotspot
disable [find]
:delay 2s
enable [find]

# ==========================================
# 6. Testing
# ==========================================

# ტესტირება browser-ში:
# http://192.168.88.1/login
# 
# შემოწმება:
# 1. Tower Group ბანერი ჩანს თუ არა
# 2. სწრაფი შესვლის ღილაკები მუშაობს თუ არა  
# 3. რეკლამაზე კლიკი იხსნება თუ არა ახალ ფანჯარაში
# 4. Auto-connect checkbox მუშაობს თუ არა

# ==========================================
# 7. Troubleshooting Commands
# ==========================================

# ფაილების სიის შესამოწმებლად:
# /file print where name~"hotspot"

# hotspot logs-ის სანახავად:
# /log print where topics~"hotspot"

# active users-ის სანახავად:
# /ip hotspot active print

# hotspot user-ების სანახავად:
# /ip hotspot user print

# ==========================================
# 8. Advanced Configuration (Optional)
# ==========================================

# Custom login page redirect:
# /ip hotspot profile
# set [find name=hsprof1] login-by=cookie,http-chap

# Session timeout configuration:
# /ip hotspot user profile
# set [find name=default] session-timeout=00:05:00

# Rate limiting:
# /ip hotspot user profile  
# set [find name=default] rate-limit=1M/1M

# ==========================================
# 9. Security Recommendations
# ==========================================

# Default password-ის შეცვლა:
# /user set admin password=YourStrongPassword

# Unnecessary services-ის გამორთვა:
# /ip service disable telnet,ftp,www
# /ip service set ssh port=2222

# Firewall rules hotspot-ისთვის:
# /ip firewall filter
# add chain=input action=accept protocol=tcp dst-port=80 comment="HTTP for Hotspot"
# add chain=input action=accept protocol=tcp dst-port=443 comment="HTTPS for Hotspot"

# ==========================================
# 10. Backup Configuration
# ==========================================

# კონფიგურაციის backup:
# /system backup save name=hotspot-backup

# Export configuration:
# /export file=hotspot-config

echo "🎉 MikroTik Hotspot configuration completed!"
echo "📱 Test at: http://192.168.88.1/login"
