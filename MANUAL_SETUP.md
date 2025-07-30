# Splito Hotspot Setup - Step by Step Guide

## Current Problem: 404 Error on MikroTik Login Page

The issue is that MikroTik is not finding custom hotspot files. Here's how to fix it:

## Solution Steps:

### Step 1: Check MikroTik Connection
✅ MikroTik is reachable (we tested this successfully)
✅ API connection works (our Laravel command works)

### Step 2: Upload Files to MikroTik

Since automatic upload has issues, use **manual upload**:

#### Method A: WinBox (Recommended)
1. Download and open WinBox
2. Connect to 192.168.88.1 with admin/Admin1.
3. Go to "Files" tab
4. Create directory structure:
   ```
   hotspot/
   hotspot/css/
   hotspot/js/
   hotspot/images/
   ```

5. Upload these files:
   ```
   From: public\hotspot\login.html
   To:   hotspot/login.html

   From: public\hotspot\css\styles.css  
   To:   hotspot/css/styles.css

   From: public\hotspot\js\app.js
   To:   hotspot/js/app.js

   From: public\hotspot\images\logo.png
   To:   hotspot/images/logo.png

   From: public\hotspot\images\tower-group-banner.jpg
   To:   hotspot/images/tower-group-banner.jpg
   ```

### Step 3: Configure MikroTik Hotspot

Connect to MikroTik Terminal (WinBox > Terminal) and run:

```routeros
# Set HTML directory for hotspot
/ip hotspot profile
set [find name=hsprof1] html-directory=hotspot

# Add walled garden entries for external sites
/ip hotspot walled-garden
add dst-host=towergroup.ge comment="Tower Group Ad"
add dst-host=splito.ge comment="Splito Main Site"
add dst-host=fonts.googleapis.com comment="Google Fonts"

# Restart hotspot service
/ip hotspot
disable [find]
enable [find]
```

### Step 4: Test

Open browser and go to: **http://192.168.88.1/login**

You should see:
- Custom login page with Tower Group banner
- Quick login buttons (Guest / Free WiFi)
- Auto-connect checkbox
- Mobile responsive design

## Alternative: Use Default MikroTik Files Location

If the above doesn't work, try uploading to root directory:

```
login.html (directly in root, not in hotspot folder)
css/styles.css
js/app.js
images/logo.png
images/tower-group-banner.jpg
```

And set HTML directory to root:
```routeros
/ip hotspot profile
set [find name=hsprof1] html-directory=""
```

## Troubleshooting

If still getting 404:
1. Check files exist: `/file print where name~"hotspot"`
2. Check hotspot profile: `/ip hotspot profile print`  
3. Check hotspot service: `/ip hotspot print`
4. Check logs: `/log print where topics~"hotspot"`

## Quick Test Commands

```routeros
# List all files
/file print

# Check hotspot configuration  
/ip hotspot profile print detail

# Check which HTML directory is set
/ip hotspot profile print value-list
```

---

**Ready to proceed with manual upload?**
