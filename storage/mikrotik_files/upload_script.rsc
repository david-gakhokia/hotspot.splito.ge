# MikroTik RouterOS Script for Custom Hotspot
# Upload these files to MikroTik and run this script

# First upload all HTML files via Files section, then run:
/ip hotspot profile set hsprof1 html-directory=hotspot
/ip hotspot profile set hsprof1 session-timeout=00:05:00
/ip hotspot profile set hsprof1 http-cookie-lifetime=00:05:00
/ip hotspot profile set hsprof1 login-by=http-chap,http-pap

# Add walled garden rules
/ip hotspot walled-garden add dst-host=splito.ge action=allow comment="Allow Splito redirect"
/ip hotspot walled-garden add dst-host=*.splito.ge action=allow comment="Allow Splito subdomains"

# Print configuration to verify
/ip hotspot profile print detail
/ip hotspot walled-garden print
