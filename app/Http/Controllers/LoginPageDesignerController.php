<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LoginPageDesignerController extends Controller
{
    protected MikrotikService $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function index()
    {
        try {
            $profiles = $this->mikrotik->getHotspotServerProfiles();
            $templates = $this->getLoginPageTemplates();
            
            return view('hotspot.login-designer.index', compact('profiles', 'templates'));
        } catch (\Exception $e) {
            Log::error('Login Page Designer Error: ' . $e->getMessage());
            return view('hotspot.login-designer.index', ['profiles' => [], 'templates' => []])
                ->with('error', 'MikroTik connection failed: ' . $e->getMessage());
        }
    }

    public function preview(Request $request)
    {
        $template = $request->input('template', 'default');
        $customization = $request->input('customization', []);
        
        $html = $this->generateLoginPageHTML($template, $customization);
        
        return view('hotspot.login-designer.preview', compact('html'));
    }

    public function customize(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'profile_id' => 'required|string',
            'title' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'background_color' => 'nullable|string|max:7',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'welcome_message' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:255',
            'custom_css' => 'nullable|string',
            'terms_text' => 'nullable|string'
        ]);

        try {
            $customization = [
                'title' => $request->title ?? 'Hotspot Login',
                'background_color' => $request->background_color ?? '#f3f4f6',
                'primary_color' => $request->primary_color ?? '#3b82f6',
                'secondary_color' => $request->secondary_color ?? '#6b7280',
                'welcome_message' => $request->welcome_message ?? 'Welcome to our WiFi network',
                'footer_text' => $request->footer_text ?? 'Powered by MikroTik',
                'custom_css' => $request->custom_css,
                'terms_text' => $request->terms_text
            ];

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('login-pages', 'public');
                $customization['logo'] = $logoPath;
            }

            $html = $this->generateLoginPageHTML($request->template, $customization);
            $filename = 'login-' . time() . '.html';
            
            // Save to storage
            Storage::disk('public')->put('login-pages/' . $filename, $html);

            // Update MikroTik profile
            $this->mikrotik->setHotspotLoginPage($request->profile_id, $filename);

            return redirect()->route('hotspot.login-designer.index')
                ->with('success', 'Login page customized and applied successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.login-designer.index')
                ->with('error', 'Failed to customize login page: ' . $e->getMessage());
        }
    }

    private function getLoginPageTemplates()
    {
        return [
            'default' => [
                'name' => 'Default',
                'description' => 'Simple and clean login form',
                'preview' => '/images/templates/default.png'
            ],
            'modern' => [
                'name' => 'Modern',
                'description' => 'Modern gradient design with animations',
                'preview' => '/images/templates/modern.png'
            ],
            'business' => [
                'name' => 'Business',
                'description' => 'Professional corporate style',
                'preview' => '/images/templates/business.png'
            ],
            'minimal' => [
                'name' => 'Minimal',
                'description' => 'Clean minimal design',
                'preview' => '/images/templates/minimal.png'
            ]
        ];
    }

    private function generateLoginPageHTML($template, $customization)
    {
        $templates = [
            'default' => $this->getDefaultTemplate($customization),
            'modern' => $this->getModernTemplate($customization),
            'business' => $this->getBusinessTemplate($customization),
            'minimal' => $this->getMinimalTemplate($customization)
        ];

        return $templates[$template] ?? $templates['default'];
    }

    private function getDefaultTemplate($customization)
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . ($customization['title'] ?? 'Hotspot Login') . '</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, ' . ($customization['background_color'] ?? '#f3f4f6') . ', #e5e7eb);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 90%;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 120px;
            height: auto;
        }
        h1 {
            color: ' . ($customization['primary_color'] ?? '#3b82f6') . ';
            text-align: center;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .welcome {
            text-align: center;
            color: ' . ($customization['secondary_color'] ?? '#6b7280') . ';
            margin-bottom: 30px;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #374151;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: ' . ($customization['primary_color'] ?? '#3b82f6') . ';
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: ' . ($customization['primary_color'] ?? '#3b82f6') . ';
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: ' . ($customization['primary_color'] ?? '#2563eb') . ';
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: ' . ($customization['secondary_color'] ?? '#6b7280') . ';
            font-size: 14px;
        }
        ' . ($customization['custom_css'] ?? '') . '
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            ' . (isset($customization['logo']) ? '<img src="' . $customization['logo'] . '" alt="Logo">' : '') . '
        </div>
        <h1>' . ($customization['title'] ?? 'Hotspot Login') . '</h1>
        <p class="welcome">' . ($customization['welcome_message'] ?? 'Welcome to our WiFi network') . '</p>
        
        <form method="post" action="$(link-login-only)">
            <input type="hidden" name="dst" value="$(link-orig)">
            <input type="hidden" name="popup" value="true">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Connect</button>
        </form>
        
        <div class="footer">
            <p>' . ($customization['footer_text'] ?? 'Powered by MikroTik') . '</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getModernTemplate($customization)
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . ($customization['title'] ?? 'Hotspot Login') . '</title>
    <style>
        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            padding: 50px;
            max-width: 450px;
            width: 90%;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 120px;
            height: auto;
            border-radius: 50%;
        }
        h1 {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 700;
        }
        .welcome {
            text-align: center;
            color: #64748b;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 14px;
        }
        ' . ($customization['custom_css'] ?? '') . '
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            ' . (isset($customization['logo']) ? '<img src="' . $customization['logo'] . '" alt="Logo">' : '') . '
        </div>
        <h1>' . ($customization['title'] ?? 'Hotspot Login') . '</h1>
        <p class="welcome">' . ($customization['welcome_message'] ?? 'Welcome to our WiFi network') . '</p>
        
        <form method="post" action="$(link-login-only)">
            <input type="hidden" name="dst" value="$(link-orig)">
            <input type="hidden" name="popup" value="true">
            
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn">Connect to WiFi</button>
        </form>
        
        <div class="footer">
            <p>' . ($customization['footer_text'] ?? 'Powered by MikroTik') . '</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getBusinessTemplate($customization)
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . ($customization['title'] ?? 'Hotspot Login') . '</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 40px;
            max-width: 400px;
            width: 90%;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        h1 {
            color: #1a202c;
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: 600;
        }
        .welcome {
            color: #64748b;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.5;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }
        ' . ($customization['custom_css'] ?? '') . '
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                ' . (isset($customization['logo']) ? '<img src="' . $customization['logo'] . '" alt="Logo">' : '') . '
            </div>
            <h1>' . ($customization['title'] ?? 'Hotspot Login') . '</h1>
        </div>
        
        <p class="welcome">' . ($customization['welcome_message'] ?? 'Welcome to our WiFi network') . '</p>
        
        <form method="post" action="$(link-login-only)">
            <input type="hidden" name="dst" value="$(link-orig)">
            <input type="hidden" name="popup" value="true">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Connect</button>
        </form>
        
        <div class="footer">
            <p>' . ($customization['footer_text'] ?? 'Powered by MikroTik') . '</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getMinimalTemplate($customization)
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . ($customization['title'] ?? 'Hotspot Login') . '</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "SF Pro Text", -apple-system, BlinkMacSystemFont, sans-serif;
            background: #ffffff;
            color: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.6;
        }
        .container {
            max-width: 320px;
            width: 90%;
            text-align: center;
        }
        .logo {
            margin-bottom: 40px;
        }
        .logo img {
            max-width: 60px;
            height: auto;
        }
        h1 {
            font-size: 32px;
            font-weight: 300;
            margin-bottom: 20px;
            color: #000000;
        }
        .welcome {
            font-size: 16px;
            color: #666666;
            margin-bottom: 40px;
            font-weight: 300;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 16px 0;
            border: none;
            border-bottom: 1px solid #e0e0e0;
            font-size: 16px;
            background: transparent;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #000000;
        }
        input::placeholder {
            color: #999999;
        }
        .btn {
            width: 100%;
            padding: 16px;
            background: #000000;
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 400;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
        }
        .btn:hover {
            background: #333333;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #999999;
        }
        ' . ($customization['custom_css'] ?? '') . '
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            ' . (isset($customization['logo']) ? '<img src="' . $customization['logo'] . '" alt="Logo">' : '') . '
        </div>
        <h1>' . ($customization['title'] ?? 'Login') . '</h1>
        <p class="welcome">' . ($customization['welcome_message'] ?? 'Please enter your credentials') . '</p>
        
        <form method="post" action="$(link-login-only)">
            <input type="hidden" name="dst" value="$(link-orig)">
            <input type="hidden" name="popup" value="true">
            
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn">Connect</button>
        </form>
        
        <div class="footer">
            <p>' . ($customization['footer_text'] ?? 'Powered by MikroTik') . '</p>
        </div>
    </div>
</body>
</html>';
    }
}
