<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\File;

class MikrotikFileManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikrotik:files 
                            {action : Action to perform (list|info|create-dir|remove|download)}
                            {--path= : File or directory path}
                            {--local= : Local file path for upload/download}
                            {--recursive : List directories recursively}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage files on MikroTik router';

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
        $action = $this->argument('action');

        try {
            switch ($action) {
                case 'list':
                    $this->listFiles();
                    break;
                    
                case 'info':
                    $this->showFileInfo();
                    break;
                    
                case 'create-dir':
                    $this->createDirectory();
                    break;
                    
                case 'remove':
                    $this->removeFile();
                    break;
                    
                case 'hotspot':
                    $this->listHotspotFiles();
                    break;
                    
                default:
                    $this->error("Unknown action: {$action}");
                    $this->info("Available actions: list, info, create-dir, remove, hotspot");
                    return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function listFiles()
    {
        $path = $this->option('path');
        $this->info('📁 MikroTik Files' . ($path ? " in: {$path}" : ''));
        $this->newLine();

        $files = $this->mikrotik->getFileList($path);

        if (empty($files)) {
            $this->warn('No files found');
            return;
        }

        $headers = ['Name', 'Type', 'Size', 'Creation Time'];
        $rows = [];

        foreach ($files as $file) {
            $rows[] = [
                $file['name'] ?? 'N/A',
                $file['type'] ?? 'N/A',
                isset($file['size']) ? $this->formatBytes($file['size']) : 'N/A',
                $file['creation-time'] ?? 'N/A'
            ];
        }

        $this->table($headers, $rows);
        $this->info("Total files: " . count($files));
    }

    protected function listHotspotFiles()
    {
        $this->info('🌐 Hotspot Files');
        $this->newLine();

        $files = $this->mikrotik->getHotspotFiles();

        if (empty($files)) {
            $this->warn('No hotspot files found');
            $this->info('💡 Tip: Upload hotspot files first using: php artisan hotspot:deploy');
            return;
        }

        $headers = ['File', 'Size', 'Last Modified'];
        $rows = [];

        foreach ($files as $file) {
            if (isset($file['name']) && str_starts_with($file['name'], 'hotspot/')) {
                $rows[] = [
                    str_replace('hotspot/', '', $file['name']),
                    isset($file['size']) ? $this->formatBytes($file['size']) : 'N/A',
                    $file['creation-time'] ?? 'N/A'
                ];
            }
        }

        if (empty($rows)) {
            $this->warn('No files in hotspot directory');
        } else {
            $this->table($headers, $rows);
        }
    }

    protected function showFileInfo()
    {
        $path = $this->option('path');
        if (!$path) {
            $this->error('--path option is required for info action');
            return;
        }

        $this->info("📄 File Information: {$path}");
        $this->newLine();

        $info = $this->mikrotik->getFileInfo($path);

        if (empty($info)) {
            $this->error('File not found');
            return;
        }

        foreach ($info as $key => $value) {
            $this->info(ucfirst(str_replace('-', ' ', $key)) . ": {$value}");
        }
    }

    protected function createDirectory()
    {
        $path = $this->option('path');
        if (!$path) {
            $this->error('--path option is required for create-dir action');
            return;
        }

        $this->info("📁 Creating directory: {$path}");
        
        $result = $this->mikrotik->createDirectory($path);
        
        if (isset($result['status']) && $result['status'] === 'exists') {
            $this->warn('Directory already exists');
        } else {
            $this->info('✅ Directory created successfully');
        }
    }

    protected function removeFile()
    {
        $path = $this->option('path');
        if (!$path) {
            $this->error('--path option is required for remove action');
            return;
        }

        if (!$this->confirm("Are you sure you want to remove: {$path}?")) {
            $this->info('Operation cancelled');
            return;
        }

        $this->info("🗑️ Removing: {$path}");
        
        $result = $this->mikrotik->removeFile($path);
        $this->info('✅ File removed successfully');
    }

    protected function formatBytes($bytes)
    {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
