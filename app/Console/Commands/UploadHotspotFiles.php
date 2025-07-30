<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikFileService;
use App\Services\MikrotikService;

class UploadHotspotFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotspot:upload 
                            {--backup : Create backup before upload}
                            {--test : Test FTP connection only}
                            {--force : Force upload without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload hotspot files to MikroTik via FTP';

    protected $fileService;

    public function __construct()
    {
        parent::__construct();
        $this->fileService = new MikrotikFileService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 MikroTik Hotspot File Upload');
        $this->newLine();

        // Test FTP connection first
        if (!$this->testConnection()) {
            return 1;
        }

        if ($this->option('test')) {
            $this->info('✅ FTP connection test successful!');
            return 0;
        }

        // Create backup if requested
        if ($this->option('backup')) {
            $this->createBackup();
        }

        // Confirm upload unless forced
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to upload hotspot files to MikroTik?')) {
                $this->info('Operation cancelled');
                return 0;
            }
        }

        // Create directory structure
        $this->createDirectories();

        // Upload files
        $this->uploadFiles();

        $this->info('🎉 Upload completed successfully!');
        $this->newLine();
        $this->info('📱 Test your hotspot at: http://' . config('mikrotik.host') . '/login');
        
        return 0;
    }

    protected function testConnection(): bool
    {
        $this->info('🔍 Testing FTP connection...');
        
        try {
            if ($this->fileService->testFTPConnection()) {
                $this->info('✅ FTP connection successful');
                return true;
            } else {
                $this->error('❌ FTP connection failed');
                $this->error('Please check:');
                $this->error('  - MikroTik FTP service is enabled: /ip service set ftp disabled=no');
                $this->error('  - Firewall allows FTP (port 21)');
                $this->error('  - Credentials in .env file are correct');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Connection error: ' . $e->getMessage());
            return false;
        }
    }

    protected function createBackup()
    {
        $this->info('💾 Creating backup...');
        
        try {
            $results = $this->fileService->backupHotspotFiles();
            
            $successCount = 0;
            foreach ($results as $file => $result) {
                if ($result['status'] === 'backed_up') {
                    $successCount++;
                }
            }
            
            if ($successCount > 0) {
                $this->info("✅ Backed up {$successCount} files");
            } else {
                $this->warn('⚠️  No files found to backup');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Backup failed: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    protected function createDirectories()
    {
        $this->info('📁 Creating directory structure...');
        
        try {
            $results = $this->fileService->createHotspotDirectories();
            
            foreach ($results as $dir => $result) {
                if (isset($result['status']) && $result['status'] === 'exists') {
                    $this->line("  📂 {$dir} (exists)");
                } elseif (isset($result['error'])) {
                    $this->line("  ❌ {$dir} - Error: {$result['error']}");
                } else {
                    $this->line("  ✅ {$dir} - Created");
                }
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Directory creation warning: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    protected function uploadFiles()
    {
        $this->info('📤 Uploading hotspot files...');
        $this->newLine();
        
        try {
            $results = $this->fileService->uploadHotspotFiles();
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($results as $remotePath => $result) {
                if ($result['status'] === 'success') {
                    $this->line("  ✅ {$remotePath}");
                    $successCount++;
                } else {
                    $this->line("  ❌ {$remotePath} - {$result['message']}");
                    $errorCount++;
                }
            }
            
            $this->newLine();
            $this->info("📊 Upload Summary:");
            $this->info("  ✅ Successful: {$successCount}");
            if ($errorCount > 0) {
                $this->warn("  ❌ Failed: {$errorCount}");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Upload failed: ' . $e->getMessage());
            return;
        }
    }
}
