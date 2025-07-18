$content = Get-Content "app\Services\MikrotikService.php" -Raw

# Define pattern to match public function lines
$pattern = '(\s*)(public function )(?!__)(.*?)(\s*\{)'

# Replace with the same content plus ensureConnection call
$replacement = '$1$2$3$4$1    $this->ensureConnection();'

# Apply the replacement
$newContent = $content -replace $pattern, $replacement

# Write back to file
$newContent | Set-Content "app\Services\MikrotikService.php" -NoNewline

Write-Host "Added ensureConnection() to all public methods"
