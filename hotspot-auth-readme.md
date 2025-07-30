
# Mikrotik Hotspot External Auth Portal

## 📍 Objective:
To decouple MikroTik Hotspot login from internal user database and authenticate users using a custom Laravel server. The Laravel server will validate MAC address (or optionally username/password) from a remote MySQL database and POST credentials back to MikroTik for login.

---

## ✅ Database Setup (on 195.201.140.99):

```sql
CREATE TABLE hotspot_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mac_address VARCHAR(17) UNIQUE,
    username VARCHAR(100),
    password VARCHAR(100),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## ✅ .env Configuration (Laravel):

```env
DB_CONNECTION=mysql
DB_HOST=195.201.140.99
DB_PORT=3306
DB_DATABASE=hotspot_db
DB_USERNAME=hotspot_user
DB_PASSWORD=supersecurepassword
```

---

## ✅ Routes

### 🔹 GET /hotspot/login

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hotspot/login', function (Request $request) {
    return view('hotspot.login', [
        'params' => $request->all()
    ]);
});
```

### 🔹 POST /hotspot/login

```php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

Route::post('/hotspot/login', function (Request $request) {
    $mac = $request->input('mac');
    $username = $request->input('username');
    $password = $request->input('password');

    // Step 1: Check user existence
    $user = DB::table('hotspot_users')
        ->where('mac_address', $mac)
        ->where('status', 'active')
        ->first();

    if (!$user) {
        return redirect('/hotspot/login?error=unauthorized');
    }

    // Step 2: Send login request to Mikrotik
    $loginUrl = $request->input('login_url') ?? $request->input('link-login');

    $response = Http::asForm()->post($loginUrl, [
        'username' => $username,
        'password' => $password,
    ]);

    return redirect($request->input('link-orig') ?? 'https://invest.mardi.ge/offer');
});
```

---

## ✅ Blade View: resources/views/hotspot/login.blade.php

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Free WiFi Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background: #f4f4f4;
        }
        .wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        .banner {
            width: 100%;
            display: block;
        }
        .overlay-text {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.8);
        }
        .form-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn-connect {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-connect:hover {
            background-color: #0056b3;
        }
        .error-msg {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <img src="{{ asset('banner.jpg') }}" alt="Banner" class="banner">
    <div class="overlay-text">Connect to Free WiFi</div>
</div>

<div class="form-container">
    @if (isset($params['link-login']))
        <form method="POST" action="{{ $params['link-login'] }}">
            <input type="hidden" name="username" value="freeuser">
            <input type="hidden" name="password" value="123">
            <input type="hidden" name="mac" value="{{ $params['mac'] ?? '' }}">
            <input type="hidden" name="link-orig" value="{{ $params['link-orig'] ?? '' }}">
            <button type="submit" class="btn-connect">Connect</button>
        </form>
    @else
        <p class="error-msg">Router login URL not provided. Please try again.</p>
    @endif
</div>

</body>
</html>
```

---

## 🧠 Prompt to use in VS Code GPT

> “Write a Laravel route that checks if a user’s MAC address exists in the database, and if so, posts login credentials to a MikroTik Hotspot login URL and redirects the user to link-orig or a promo page.”

---

## ✅ Next steps

- Create Seeder for demo users
- Add logging (optional)
- Add admin dashboard (future)
