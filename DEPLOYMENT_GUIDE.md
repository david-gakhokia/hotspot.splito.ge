# 🚀 Splito Hotspot - MikroTik Deployment Guide

## 📋 Overview
ეს არის სრული guide MikroTik RouterOS-ზე custom hotspot login page-ის განსალაგებლად Tower Group რეკლამით და ავტომატური კავშირით.

## 🔧 Prerequisites

### MikroTik Configuration
1. **FTP Service Enable** (ფაილების ატვირთვისთვის):
   ```routeros
   /ip service
   set ftp disabled=no
   ```

2. **Hotspot Service** უნდა იყოს კონფიგურებული
3. **Admin Access** RouterOS-ზე

### Development Environment
- Laravel Herd ✅ (უკვე კონფიგურებულია)
- PowerShell (Windows)
- WinBox (optional, თუ GUI გსურთ)

## 📁 File Structure
```
public/hotspot/
├── login.html              # Main login page with Tower Group ad
├── css/styles.css          # All styles including ad banner
├── js/app.js              # JavaScript with auto-connect & analytics
└── images/
    ├── logo.png           # Splito logo
    └── tower-group-banner.jpg  # Tower Group advertisement image
```

## 🚀 Deployment Steps

### Step 1: Test Local Development
```bash
# Laravel Herd ავტომატურად მუშაობს
http://hotspot.splito.test/test-hotspot
http://hotspot.splito.test/hotspot/login.html
```

### Step 2: Test MikroTik Connection
```bash
cd c:\Users\Davit\Herd\hotspot.splito.ge
php artisan hotspot:deploy --test
```

### Step 3: Upload Files to MikroTik

#### Option A: PowerShell Script (Recommended)
```powershell
# Default settings (.env ფაილიდან)
.\mikrotik-upload.ps1

# Custom settings
.\mikrotik-upload.ps1 -MikrotikIP "192.168.1.1" -Username "admin" -Password "yourpass"

# Test only
.\mikrotik-upload.ps1 -TestOnly
```

#### Option B: Manual Upload via WinBox
1. Open WinBox
2. Connect to router
3. Go to Files tab
4. Create directory structure:
   - hotspot/
   - hotspot/css/
   - hotspot/js/
   - hotspot/images/
5. Upload all files to respective directories

### Step 4: Configure MikroTik
```bash
# Connect via SSH or WinBox Terminal
ssh admin@192.168.88.1

# Run configuration script
# ან copy-paste commands from mikrotik-setup.rsc
```

Key Commands:
```routeros
# Set HTML directory
/ip hotspot profile
set [find name=hsprof1] html-directory=hotspot

# Add walled garden entries
/ip hotspot walled-garden
add dst-host=towergroup.ge comment="Tower Group Ad"
add dst-host=splito.ge comment="Splito Main Site"

# Restart hotspot service
/ip hotspot
disable [find]
enable [find]
```

## 🧪 Testing

### Test URLs
- **Hotspot Login**: `http://192.168.88.1/login`
- **Status Page**: `http://192.168.88.1/status`

### Test Scenarios
1. **Normal Login**: შეიყვანეთ username/password
2. **Quick Login**: გამოიყენეთ "სტუმარი" ღილაკი
3. **Auto-Connect**: ჩართეთ checkbox და ტესტი login
4. **Ad Banner**: დააჭირეთ Tower Group ბანერს
5. **Mobile View**: ტესტი მობილურ მოწყობილობაზე

## 📊 Analytics Setup

### Google Analytics Integration
1. Get GA4 Tracking ID from Google Analytics
2. Update login.html:
   ```html
   <script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"></script>
   <script>
     window.dataLayer = window.dataLayer || [];
     function gtag(){dataLayer.push(arguments);}
     gtag('js', new Date());
     gtag('config', 'YOUR_GA4_TRACKING_ID');
   </script>
   ```

### Tracked Events
- `hotspot_page_view` - გვერდის ნახვა
- `ad_impression` - რეკლამის ჩვენება
- `ad_click` - რეკლამაზე კლიკი
- `login_attempt` - login მცდელობა
- `login_success` - წარმატებული login
- `quick_login_used` - სწრაფი login გამოყენება
- `auto_connect_toggled` - auto-connect ჩართვა/გამორთვა

## 🎨 Customization

### Tower Group Brand Update
1. **Images**: შეანაცვლეთ `tower-group-banner.jpg` რეალური სურათით
2. **Domain**: `towergroup.ge` შეცვალეთ რეალური domain-ით
3. **Colors**: განაახლეთ CSS ცვლადები:
   ```css
   :root {
     --ad-gradient: linear-gradient(135deg, #YOUR_COLOR1, #YOUR_COLOR2);
   }
   ```

### Content Updates
- **Text**: `login.html`-ში შეცვალეთ ქართული ტექსტები
- **Logo**: შეანაცვლეთ Splito ლოგო
- **Styling**: `css/styles.css`-ში custom სტილები

## 🔧 Troubleshooting

### Common Issues

#### Files Not Loading
```routeros
# Check file list
/file print where name~"hotspot"

# Check permissions
/ip hotspot profile print
```

#### Login Not Working
```routeros
# Check hotspot users
/ip hotspot user print

# Check active sessions
/ip hotspot active print

# Check logs
/log print where topics~"hotspot"
```

#### Walled Garden Issues
```routeros
# List walled garden entries
/ip hotspot walled-garden print

# Test specific domain
/tool netwatch add host=towergroup.ge
```

### Debug Mode
Enable debug in `js/app.js`:
```javascript
// Set to true for detailed console logging
const DEBUG_MODE = true;
```

## 📱 Mobile Optimization

### Responsive Design
- ყველა ელემენტი responsive-ია
- Mobile-first approach
- Touch-friendly buttons
- Optimized images

### Performance
- CSS minification (production-ისთვის)
- Image compression
- Lazy loading (future enhancement)

## 🔒 Security Considerations

### MikroTik Security
```routeros
# Change default password
/user set admin password=StrongPassword123

# Disable unnecessary services
/ip service disable telnet,ftp,www

# Secure SSH
/ip service set ssh port=2222
```

### Hotspot Security
- Rate limiting per user
- Session timeouts
- MAC address filtering (optional)

## 📈 Performance Monitoring

### Key Metrics
- Login success rate
- Ad click-through rate
- Session duration
- Popular quick login options
- Auto-connect usage

### RouterOS Monitoring
```routeros
# Resource usage
/system resource print

# Hotspot statistics
/ip hotspot print stats
```

## 🚀 Next Steps

### Phase 2 Enhancements
1. **Multiple Advertisers**: Rotation system
2. **A/B Testing**: Different ad creatives
3. **Personalization**: User behavior tracking
4. **Offline Support**: Service worker
5. **API Integration**: Real-time analytics

### Integration Options
- **Payment Gateway**: Paid internet access
- **Social Login**: Facebook/Google login
- **SMS Verification**: Phone number verification
- **Voucher System**: Pre-paid codes

## 📞 Support

### Laravel Issues
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### MikroTik Issues
- RouterOS Documentation
- MikroTik Forum
- Local MikroTik Support

---

**🎉 Success!** თქვენი custom hotspot login page მზაარაა Tower Group რეკლამითა და ავტომატური კავშირით!
