#!/usr/bin/env php
<?php

$filename = 'app/Services/MikrotikService.php';
$content = file_get_contents($filename);

// Find all public function declarations (except constructors/destructors)
$lines = explode("\n", $content);
$new_lines = [];
$in_function_body = false;

foreach ($lines as $i => $line) {
    $new_lines[] = $line;
    
    // Check if this is a public function line (not constructor/destructor)
    if (preg_match('/^\s*public function (?!__)(.*?)\s*\(/', $line)) {
        // Check if the next line with content is opening brace
        $j = $i + 1;
        while ($j < count($lines) && trim($lines[$j]) === '') {
            $j++;
        }
        
        if ($j < count($lines) && trim($lines[$j]) === '{') {
            // Check if ensureConnection is not already there
            $k = $j + 1;
            while ($k < count($lines) && trim($lines[$k]) === '') {
                $k++;
            }
            
            if ($k < count($lines) && strpos($lines[$k], 'ensureConnection') === false) {
                // Add ensureConnection after the opening brace
                array_splice($new_lines, count($new_lines), 0, ['        $this->ensureConnection();']);
            }
        }
    }
}

file_put_contents($filename, implode("\n", $new_lines));
echo "Updated MikrotikService.php with ensureConnection() calls\n";
