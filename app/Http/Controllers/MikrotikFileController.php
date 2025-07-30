<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MikrotikFileService;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Storage;

class MikrotikFileController extends Controller
{
    protected $fileService;
    protected $mikrotik;

    public function __construct()
    {
        $this->fileService = new MikrotikFileService();
        $this->mikrotik = new MikrotikService();
    }

    /**
     * Show file manager interface
     */
    public function index()
    {
        try {
            $files = $this->mikrotik->getFileList();
            $hotspotFiles = $this->mikrotik->getHotspotFiles();
            
            return view('mikrotik.files', compact('files', 'hotspotFiles'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load files: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload files via web interface
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max
            'target_directory' => 'required|string'
        ]);

        try {
            $results = [];
            $targetDir = $request->target_directory;

            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $remotePath = $targetDir . '/' . $filename;
                
                // Save temporarily
                $tempPath = $file->storeAs('temp', $filename);
                $localPath = storage_path('app/' . $tempPath);
                
                try {
                    $this->fileService->uploadFileViaFTP($localPath, $remotePath);
                    $results[] = ['file' => $filename, 'status' => 'success'];
                } catch (\Exception $e) {
                    $results[] = ['file' => $filename, 'status' => 'error', 'message' => $e->getMessage()];
                } finally {
                    // Clean up temp file
                    Storage::delete($tempPath);
                }
            }

            return response()->json(['results' => $results]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download file from MikroTik
     */
    public function download(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        try {
            $remotePath = $request->file_path;
            $filename = basename($remotePath);
            $tempPath = storage_path('app/temp/' . $filename);
            
            // Create temp directory if not exists
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            $this->fileService->downloadFileViaFTP($remotePath, $tempPath);
            
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Download failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete file from MikroTik
     */
    public function delete(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        try {
            $this->mikrotik->removeFile($request->file_path);
            
            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload hotspot files via web interface
     */
    public function uploadHotspotFiles()
    {
        try {
            // Create directories first
            $this->fileService->createHotspotDirectories();
            
            // Upload files
            $results = $this->fileService->uploadHotspotFiles();
            
            $success = 0;
            $errors = [];
            
            foreach ($results as $file => $result) {
                if ($result['status'] === 'success') {
                    $success++;
                } else {
                    $errors[] = $file . ': ' . $result['message'];
                }
            }
            
            $message = "Successfully uploaded {$success} files";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }
            
            return response()->json([
                'success' => $success > 0,
                'message' => $message,
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Test FTP connection
     */
    public function testConnection()
    {
        try {
            $isConnected = $this->fileService->testFTPConnection();
            
            return response()->json([
                'connected' => $isConnected,
                'message' => $isConnected ? 'FTP connection successful' : 'FTP connection failed'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get file information
     */
    public function fileInfo(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        try {
            $info = $this->mikrotik->getFileInfo($request->file_path);
            
            return response()->json(['info' => $info]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
