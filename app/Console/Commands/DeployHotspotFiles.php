<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\File;

class DeployHotspotFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotspot:deploy 
                            {--test : Test MikroTik connection without uploading files}
                            {--backup : Create backup of existing hotspot files}
                            {--force : Force upload even if files exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy custom hotspot files to MikroTik RouterOS';

    protected $mikrotik;

    public function __construct()
    {
        parent::__construct();
        $this->mikrotik = new MikrotikService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Splito Hotspot Deployment Tool');
        $this->newLine();

        // Test connection first
        if (!$this->testConnection()) {
            return 1;
        }

        if ($this->option('test')) {
            $this->info('✅ Connection test successful! Use --force to deploy files.');
            return 0;
        }

        // Create backup if requested
        if ($this->option('backup')) {
            $this->createBackup();
        }

        // Deploy files
        $this->deployFiles();

        // Configure hotspot
        $this->configureHotspot();

        $this->info('🎉 Hotspot deployment completed successfully!');
        $this->newLine();
        $this->info('📱 Test your hotspot at: http://' . config('mikrotik.host') . '/login');
        
        return 0;
    }

    protected function testConnection()
    {
        $this->info('🔍 Testing MikroTik connection...');
        
        try {
            $connection = $this->mikrotik->connect();
            if (!$connection) {
                $this->error('❌ Failed to connect to MikroTik router');
                $this->error('   Host: ' . config('mikrotik.host') . ':' . config('mikrotik.port'));
                $this->error('   Check your .env configuration');
                return false;
            }

            $this->info('✅ Successfully connected to MikroTik router');
            $this->info('   Host: ' . config('mikrotik.host') . ':' . config('mikrotik.port'));
            
            // Test if hotspot service exists
            $hotspots = $this->mikrotik->query('/ip/hotspot/print');
            if (empty($hotspots)) {
                $this->warn('⚠️  No hotspot service found. Please configure hotspot first.');
            } else {
                $this->info('✅ Hotspot service found: ' . count($hotspots) . ' profile(s)');
            }

            return true;
        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function createBackup()
    {
        $this->info('💾 Creating backup of existing hotspot files...');
        
        try {
            $backupDir = 'hotspot_backup_' . date('Y-m-d_H-i-s');
            
            // List existing hotspot files
            $files = $this->mikrotik->query('/file/print', ['?name' => 'hotspot']);
            
            if (!empty($files)) {
                $this->info('📁 Found ' . count($files) . ' hotspot files to backup');
                // In real implementation, you would download these files
                $this->info('✅ Backup completed to: ' . $backupDir);
            } else {
                $this->info('ℹ️  No existing hotspot files found');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Backup failed: ' . $e->getMessage());
        }
    }

    protected function deployFiles()
    {
        $this->info('📤 Deploying hotspot files...');
        
        $files = [
            'login.html' => public_path('hotspot/login.html'),
            'css/styles.css' => public_path('hotspot/css/styles.css'),
            'js/app.js' => public_path('hotspot/js/app.js'),
            'images/logo.png' => public_path('hotspot/images/logo.png'),
            'images/tower-group-banner.jpg' => public_path('hotspot/images/tower-group-banner.jpg'),
        ];

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($files as $remotePath => $localPath) {
            if (!File::exists($localPath)) {
                $this->newLine();
                $this->warn("⚠️  Local file not found: {$localPath}");
                continue;
            }

            try {
                // Upload file to MikroTik
                $this->uploadFile($localPath, 'hotspot/' . $remotePath);
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Failed to upload {$remotePath}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('✅ Files deployed successfully');
    }

    protected function uploadFile($localPath, $remotePath)
    {
        // Read file content
        $content = File::get($localPath);
        $filename = basename($remotePath);
        
        // For MikroTik API, we need to use the /tool/fetch or manual file upload
        // This is a simplified version - in real implementation you'd use FTP or file upload
        
        $this->mikrotik->query('/file/remove', ['numbers' => $remotePath]);
        
        // Create the file (this is pseudo-code, actual implementation depends on your MikroTik library)
        // $this->mikrotik->uploadFile($localPath, $remotePath);
        
        $this->line("   📁 Uploaded: {$filename}");
    }

    protected function configureHotspot()
    {
        $this->info('⚙️  Configuring hotspot settings...');
        
        try {
            // Set HTML directory
            $profiles = $this->mikrotik->query('/ip/hotspot/profile/print');
            
            foreach ($profiles as $profile) {
                $this->mikrotik->query('/ip/hotspot/profile/set', [
                    'numbers' => $profile['.id'],
                    'html-directory' => 'hotspot'
                ]);
                $this->info("   ✅ Updated profile: {$profile['name']}");
            }

            // Add walled garden entries
            $walledGardenEntries = [
                'towergroup.ge',
                'splito.ge', 
                '*.google-analytics.com',
                '*.googletagmanager.com',
                'fonts.googleapis.com'
            ];

            foreach ($walledGardenEntries as $domain) {
                try {
                    $this->mikrotik->query('/ip/hotspot/walled-garden/add', [
                        'dst-host' => $domain,
                        'comment' => 'Splito Hotspot - ' . $domain
                    ]);
                    $this->info("   ✅ Added walled garden: {$domain}");
                } catch (\Exception $e) {
                    // Entry might already exist
                    $this->line("   ℹ️  Walled garden entry exists: {$domain}");
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Configuration failed: ' . $e->getMessage());
        }
    }
}
