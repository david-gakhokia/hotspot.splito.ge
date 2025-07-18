<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';

use App\Services\MikrotikService;

// Test MikroTik connection
echo "Testing MikroTik connection...\n";

try {
    $service = new MikrotikService();
    echo "✓ Connection established\n";
    
    echo "Getting hotspot users...\n";
    $users = $service->getHotspotUsers();
    echo "✓ Users retrieved: " . count($users) . "\n";
    
    if (count($users) > 0) {
        echo "First user data:\n";
        print_r($users[0]);
    } else {
        echo "No users found\n";
    }
    
    echo "Getting profiles...\n";
    $profiles = $service->getHotspotProfiles();
    echo "✓ Profiles retrieved: " . count($profiles) . "\n";
    
    if (count($profiles) > 0) {
        echo "Available profiles:\n";
        foreach ($profiles as $profile) {
            echo "- " . ($profile['name'] ?? 'Unknown') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
