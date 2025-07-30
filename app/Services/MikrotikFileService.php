<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class MikrotikFileService
{
    protected $mikrotik;
    protected $host;
    protected $username;
    protected $password;

    public function __construct(MikrotikService $mikrotik = null)
    {
        $this->mikrotik = $mikrotik ?: new MikrotikService();
        $this->host = config('mikrotik.host');
        $this->username = config('mikrotik.user');
        $this->password = config('mikrotik.pass');
    }

    /**
     * Upload file to MikroTik via FTP
     */
    public function uploadFileViaFTP(string $localPath, string $remotePath): bool
    {
        if (!file_exists($localPath)) {
            throw new Exception("Local file does not exist: {$localPath}");
        }

        try {
            // Create FTP URL
            $ftpUrl = "ftp://{$this->username}:{$this->password}@{$this->host}/{$remotePath}";
            
            // Read file content
            $fileContent = file_get_contents($localPath);
            
            // Upload using file_put_contents with FTP wrapper
            $result = file_put_contents($ftpUrl, $fileContent);
            
            if ($result === false) {
                throw new Exception("FTP upload failed");
            }

            Log::info("File uploaded successfully", [
                'local' => $localPath,
                'remote' => $remotePath,
                'size' => strlen($fileContent)
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("FTP upload failed", [
                'error' => $e->getMessage(),
                'local' => $localPath,
                'remote' => $remotePath
            ]);
            throw $e;
        }
    }

    /**
     * Upload multiple files to MikroTik
     */
    public function uploadMultipleFiles(array $files): array
    {
        $results = [];
        
        foreach ($files as $localPath => $remotePath) {
            try {
                $this->uploadFileViaFTP($localPath, $remotePath);
                $results[$remotePath] = ['status' => 'success'];
            } catch (Exception $e) {
                $results[$remotePath] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Download file from MikroTik via FTP
     */
    public function downloadFileViaFTP(string $remotePath, string $localPath): bool
    {
        try {
            // Create FTP URL
            $ftpUrl = "ftp://{$this->username}:{$this->password}@{$this->host}/{$remotePath}";
            
            // Download file
            $content = file_get_contents($ftpUrl);
            
            if ($content === false) {
                throw new Exception("FTP download failed");
            }

            // Save to local file
            $result = file_put_contents($localPath, $content);
            
            if ($result === false) {
                throw new Exception("Failed to save local file");
            }

            Log::info("File downloaded successfully", [
                'remote' => $remotePath,
                'local' => $localPath,
                'size' => strlen($content)
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("FTP download failed", [
                'error' => $e->getMessage(),
                'remote' => $remotePath,
                'local' => $localPath
            ]);
            throw $e;
        }
    }

    /**
     * Upload hotspot files
     */
    public function uploadHotspotFiles(): array
    {
        $hotspotFiles = [
            public_path('hotspot/login.html') => 'hotspot/login.html',
            public_path('hotspot/logout.html') => 'hotspot/logout.html',
            public_path('hotspot/status.html') => 'hotspot/status.html',
            public_path('hotspot/error.html') => 'hotspot/error.html',
            public_path('hotspot/css/styles.css') => 'hotspot/css/styles.css',
            public_path('hotspot/js/app.js') => 'hotspot/js/app.js',
        ];

        // Add image files
        $imageFiles = [
            public_path('hotspot/images/logo.png') => 'hotspot/images/logo.png',
            public_path('hotspot/images/tower-group-banner.jpg') => 'hotspot/images/tower-group-banner.jpg',
        ];

        $allFiles = array_merge($hotspotFiles, $imageFiles);
        
        // Filter only existing files
        $existingFiles = [];
        foreach ($allFiles as $local => $remote) {
            if (file_exists($local)) {
                $existingFiles[$local] = $remote;
            }
        }

        if (empty($existingFiles)) {
            throw new Exception("No hotspot files found to upload");
        }

        return $this->uploadMultipleFiles($existingFiles);
    }

    /**
     * Create hotspot directory structure
     */
    public function createHotspotDirectories(): array
    {
        $directories = [
            'hotspot',
            'hotspot/css',
            'hotspot/js',
            'hotspot/images'
        ];

        $results = [];
        
        foreach ($directories as $dir) {
            try {
                $result = $this->mikrotik->createDirectory($dir);
                $results[$dir] = $result;
            } catch (Exception $e) {
                $results[$dir] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Test FTP connection
     */
    public function testFTPConnection(): bool
    {
        try {
            // Try to list root directory
            $ftpUrl = "ftp://{$this->username}:{$this->password}@{$this->host}/";
            
            // Use stream context for better error handling
            $context = stream_context_create([
                'ftp' => [
                    'timeout' => 10
                ]
            ]);
            
            $handle = fopen($ftpUrl, 'r', false, $context);
            
            if ($handle === false) {
                return false;
            }
            
            fclose($handle);
            return true;
            
        } catch (Exception $e) {
            Log::error("FTP connection test failed", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Backup existing hotspot files
     */
    public function backupHotspotFiles(string $backupDir = null): array
    {
        if (!$backupDir) {
            $backupDir = storage_path('mikrotik_backups/' . date('Y-m-d_H-i-s'));
        }

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $hotspotFiles = $this->mikrotik->getHotspotFiles();
        $results = [];

        foreach ($hotspotFiles as $file) {
            if (!isset($file['name'])) continue;
            
            $remotePath = $file['name'];
            $localPath = $backupDir . '/' . basename($remotePath);
            
            try {
                $this->downloadFileViaFTP($remotePath, $localPath);
                $results[$remotePath] = ['status' => 'backed_up', 'local' => $localPath];
            } catch (Exception $e) {
                $results[$remotePath] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }
}
