# 🔥 WiFi Hotspot Authentication System

> **ზე-თანამედროვე WiFi ავტენტიფიკაციის სისტემა Laravel 12 და MikroTik RouterOS-ით**

[![Laravel 12](https://img.shields.io/badge/Laravel-12-ff2d20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777bb4?style=for-the-badge&logo=php)](https://php.net)
[![MikroTik](https://img.shields.io/badge/MikroTik-RouterOS-00a8ff?style=for-the-badge&logo=mikrotik)](https://mikrotik.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38b2ac?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)

## 📖 მოკლე აღწერა

ეს არის სრულფასოვანი WiFi Hotspot ავტენტიფიკაციის სისტემა, რომელიც შექმნილია Laravel 12 ფრეიმვორკით და ინტეგრირებულია MikroTik RouterOS-თან. სისტემა უზრუნველყოფს WiFi მომხმარებლების ავტენტიფიკაციას, სესიების მონიტორინგს და ადმინისტრაციულ მართვას.

## ✨ მთავარი ფუნქციები

### 🚀 WiFi ავტენტიფიკაცია
- **ულამაზესი Login Portal** - Glass Morphism დიზაინით
- **რეალურ დროში სესიის მონიტორინგი** - ონლაინ მომხმარებლების თვალყურის დევნება
- **მობილური Responsive** - ყველა მოწყობილობაზე სრულყოფილი ხედვა
- **Test მომხმარებლები** - სწრაფი ტესტირებისთვის

### 🛠️ MikroTik ინტეგრაცია
- **RouterBoard 951Ui-2HnD** მხარდაჭერა
- **Binary API** კავშირი (პორტი 8728)
- **Firmware 6.49.6** თავსებადობა
- **User Management** - მომხმარებლების მართვა

### 📊 ადმინისტრაციული პანელი
- **Service Management** - Hotspot სერვისის კონტროლი
- **Active Sessions** - აქტიური სესიების მონიტორინგი
- **Real-time Statistics** - რეალურ დროში სტატისტიკა
- **System Information** - სისტემის ინფორმაცია

## 🏗️ ტექნოლოგიები

### Backend
- **Laravel 12** - PHP ფრეიმვორკი
- **PHP 8.3+** - უახლესი PHP ვერსია
- **SQLite** - მონაცემთა ბაზა
- **MikroTik Binary API** - RouterOS ინტეგრაცია

### Frontend
- **Alpine.js** - რეაქტიული JavaScript ფრეიმვორკი
- **Tailwind CSS** - Utility-first CSS ფრეიმვორკი
- **Blade Templates** - Laravel-ის templating engine
- **Glass Morphism** - თანამედროვე UI დიზაინი

### დეველოპმენტი
- **Laravel Herd** - Local დეველოპმენტ environment
- **Vite** - Frontend build ტული
- **Pest PHP** - Testing ფრეიმვორკი

## 🚀 ინსტალაცია

### 1. Repository-ის კლონირება
```bash
git clone https://github.com/david-gakhokia/hotspot.splito.ge.git
cd hotspot.splito.ge
```

### 2. Dependencies-ის ინსტალაცია
```bash
composer install
npm install
```

### 3. Environment კონფიგურაცია
```bash
cp .env.example .env
php artisan key:generate
```

### 4. მონაცემთა ბაზის მომზადება
```bash
php artisan migrate
php artisan db:seed
```

### 5. Frontend Assets-ის კომპილაცია
```bash
npm run build
```

### 6. MikroTik კონფიგურაცია
`.env` ფაილში დააყენეთ MikroTik-ის პარამეტრები:
```env
MIKROTIK_HOST=192.168.88.1
MIKROTIK_USERNAME=admin
MIKROTIK_PASSWORD=your_password
MIKROTIK_PORT=8728
```

## 🎯 გამოყენება

### Hotspot სერვისის გაშვება
```bash
php artisan hotspot:enable bridge
```

### დეველოპმენტ სერვერის გაშვება
```bash
php artisan serve
```

### მთავარი URL-ები
- **დაშბორდი**: `http://localhost:8000/dashboard`
- **WiFi Login Portal**: `http://localhost:8000/hotspot/login`
- **Welcome Page**: `http://localhost:8000/hotspot/welcome`
- **Management Panel**: `http://localhost:8000/hotspot/management`

## 🧪 ტესტირება

### Test მომხმარებლები WiFi-ისთვის:
- **Username**: `test` | **Password**: `test123`
- **Username**: `demo` | **Password**: `demo123`
- **Username**: `guest` | **Password**: `guest123`

### Unit ტესტების გაშვება
```bash
php artisan test
```

## 📁 პროექტის სტრუქტურა

```
├── app/
│   ├── Http/Controllers/
│   │   └── HotspotAuthController.php    # WiFi ავტენტიფიკაცია
│   ├── Console/Commands/
│   │   └── EnableHotspotService.php     # Hotspot სერვისის გაშვება
│   └── Services/
│       └── MikrotikService.php          # MikroTik API ინტეგრაცია
├── resources/views/
│   ├── hotspot/
│   │   ├── login.blade.php              # WiFi Login Portal
│   │   ├── welcome.blade.php            # Post-login გვერდი
│   │   └── management.blade.php         # ადმინ პანელი
│   └── docs/
│       └── mikrotik-system.blade.php    # სისტემის დოკუმენტაცია
├── routes/
│   └── web.php                          # Routes კონფიგურაცია
└── config/
    └── mikrotik.php                     # MikroTik კონფიგურაცია
```

## 🔧 MikroTik RouterOS კონფიგურაცია

### Binary API გააქტიურება
```bash
/ip service
set api disabled=no port=8728
```

### Hotspot კონფიგურაცია
```bash
/ip hotspot
add name=hotspot1 interface=bridge
/ip hotspot profile
set hsprof1 login-by=http-chap,http-pap
```

### User Manager
```bash
/ip hotspot user
add name=test password=test123 profile=default
add name=demo password=demo123 profile=default
add name=guest password=guest123 profile=default
```

## 📚 დოკუმენტაცია

დეტალური დოკუმენტაცია ხელმისაწვდომია:
- **სისტემის დოკუმენტაცია**: `/docs/mikrotik-system`
- **API Reference**: `/docs/api`
- **Installation Guide**: `/docs/installation`

## 🛡️ უსაფრთხოება

- **CSRF Protection** - Laravel-ის built-in დაცვა
- **Session Management** - უსაფრთხო სესიების მართვა
- **Input Validation** - ყველა input-ის ვალიდაცია
- **API Authentication** - MikroTik API უსაფრთხო კავშირი

## 🚀 Production Deployment

### Nginx კონფიგურაცია
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/hotspot.splito.ge/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### SSL Certificate
```bash
certbot --nginx -d your-domain.com
```

## 🔄 Updates & Maintenance

### კოდის განახლება
```bash
git pull origin main
composer install --no-dev
npm run build
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

ეს პროექტი ლიცენზირებულია MIT License-ით. იხილეთ [LICENSE](LICENSE) ფაილი დეტალებისთვის.

## 👨‍💻 ავტორი

**David Gakhokia**
- GitHub: [@david-gakhokia](https://github.com/david-gakhokia)
- Email: david@splito.ge

## 🙏 მადლობა

- [Laravel](https://laravel.com) - შესანიშნავი PHP ფრეიმვორკისთვის
- [MikroTik](https://mikrotik.com) - RouterOS და API-ისთვის
- [Tailwind CSS](https://tailwindcss.com) - ულამაზესი CSS ფრეიმვორკისთვის
- [Alpine.js](https://alpinejs.dev) - მარტივი რეაქტიულობისთვის

---

<div align="center">
  <strong>🔥 შექმნილია ❤️-ით საქართველოში 🇬🇪</strong>
</div>
