# MikroTik ფაილების წვდომა - სრული გაიდი

## 🎯 პასუხი თქვენს კითხვაზე

**კი, MikroTik-ის ფაილებზე პირდაპირი წვდომა შეგვიძლია!** ამ პროექტში შექმნილია სამი მთავარი მეთოდი:

## 🔧 ფაილების წვდომის მეთოდები

### 1. **API არხით** (RouterOS API)
```bash
# ფაილების სიის მისაღებად
php artisan mikrotik:files list

# hotspot ფაილების სიისთვის  
php artisan mikrotik:files hotspot

# ფაილის ინფორმაციისთვის
php artisan mikrotik:files info --path="hotspot/login.html"

# დირექტორიის შექმნა
php artisan mikrotik:files create-dir --path="hotspot"
```

### 2. **FTP არხით** (ყველაზე ეფექტური)
```bash
# FTP კავშირის ტესტი
php artisan hotspot:upload --test

# hotspot ფაილების ატვირთვა backup-ით
php artisan hotspot:upload --backup

# იძულებითი ატვირთვა
php artisan hotspot:upload --force
```

### 3. **Web Interface-ით**
```
http://localhost/mikrotik/files
```

## ⚙️ MikroTik-ის კონფიგურაცია

### FTP სერვისის ჩართვა
```RouterOS
# SSH/WinBox-ით შედით MikroTik-ში და გაუშვით:
/ip service set ftp disabled=no port=21

# ან კონკრეტული IP-სთვის
/ip firewall filter add chain=input protocol=tcp dst-port=21 src-address=192.168.88.0/24 action=accept
```

### API სერვისის ჩართვა
```RouterOS
/ip service set api disabled=no port=8728
/ip service set api-ssl disabled=no port=8729
```

## 📁 ფაილების სტრუქტურა MikroTik-ში

```
/ (root)
├── hotspot/
│   ├── login.html
│   ├── logout.html
│   ├── status.html
│   ├── error.html
│   ├── css/
│   │   └── styles.css
│   ├── js/
│   │   └── app.js
│   └── images/
│       ├── logo.png
│       └── tower-group-banner.jpg
├── pub/
└── flash/
```

## 🚀 სწრაფი გამოყენება

### 1. FTP-ის ჩართვა MikroTik-ზე
```bash
# WinBox ან SSH-ით:
/ip service print
/ip service set ftp disabled=no
```

### 2. კავშირის ტესტი
```bash
php artisan hotspot:upload --test
```

### 3. ფაილების ატვირთვა
```bash
php artisan hotspot:upload --backup --force
```

## 🔍 ხშირი პრობლემები და გადაჭრა

### ❌ "FTP connection failed"
```bash
# შემოწმება:
1. /ip service print  # FTP disabled=no უნდა იყოს
2. /ip firewall filter print  # FTP პორტი 21 ღია უნდა იყოს  
3. ping 192.168.88.1  # ქსელური კავშირი
```

### ❌ "API connection failed"  
```bash
# შემოწმება:
1. /ip service set api disabled=no
2. .env ფაილში სწორი credentials
3. /user print  # admin user არსებობს
```

### ❌ "Files not found"
```bash
# ფაილების შექმნა:
php artisan hotspot:deploy --force
```

## 📋 PHP API მეთოდები

```php
use App\Services\MikrotikService;
use App\Services\MikrotikFileService;

// ფაილების სია
$mikrotik = new MikrotikService();
$files = $mikrotik->getFileList();
$hotspotFiles = $mikrotik->getHotspotFiles();

// ფაილების ატვირთვა
$fileService = new MikrotikFileService();
$fileService->uploadFileViaFTP($localPath, $remotePath);
$fileService->uploadHotspotFiles();

// ფაილის წაშლა
$mikrotik->removeFile('hotspot/old-file.html');

// დირექტორიის შექმნა
$mikrotik->createDirectory('hotspot/new-folder');
```

## 🌐 Web Interface

### ფაილების მართვა ბრაუზერით:
1. გადადით: `http://localhost/mikrotik/files`
2. ფაილების ატვირთვა drag & drop-ით
3. ფაილების ჩამოტვირთვა
4. ფაილების წაშლა
5. რეალ ტაიმ სტატუსი

## 🎯 რეკომენდაციები

### მარტივი გამოყენებისთვის:
1. **FTP მეთოდი** - ყველაზე სტაბილური
2. **PowerShell სკრიპტები** - Windows მომხმარებლებისთვის
3. **Web Interface** - გრაფიკული მომხმარებლებისთვის

### პროფესიონალური გამოყენებისთვის:
1. **API მეთოდები** - ავტომატიზაციისთვის
2. **Artisan Commands** - DevOps პროცესებისთვის
3. **Backup სისტემა** - უსაფრთხოებისთვის

## 🔒 უსაფრთხოება

```bash
# Backup-ის შექმნა
php artisan hotspot:upload --backup

# ფაილების backup ხელით
$fileService->backupHotspotFiles('/path/to/backup');
```

ეს ყველაფერი უზრუნველყოფს MikroTik-ის ფაილებზე სრულ კონტროლს! 🎉
