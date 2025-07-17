<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MikrotikService
{
    protected $socket;
    protected $host;
    protected $port;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->host = config('mikrotik.host', '192.168.88.1');
        $this->port = config('mikrotik.port', 8728);
        $this->username = config('mikrotik.user');
        $this->password = config('mikrotik.pass');

        $this->connect();
        $this->login();
    }

    public function __destruct()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }

    protected function connect()
    {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 3);
        if (!$this->socket) {
            throw new \Exception("Mikrotik connection failed: $errstr ($errno)");
        }
        Log::info("MikroTik connection established to {$this->host}:{$this->port}");
    }

    protected function login()
    {
        $this->write(['/login', '=name=' . $this->username, '=password=' . $this->password]);
        $response = $this->read();
        if (!isset($response[0]) || strpos($response[0], '!done') === false) {
            throw new \Exception('Mikrotik login failed');
        }
        Log::info("MikroTik login successful for user: {$this->username}");
    }

    public function getActiveHotspotUsers(): array
    {
        $this->write(['/ip/hotspot/active/print']);
        $response = $this->read();

        $users = [];
        $current = [];

        foreach ($response as $line) {
            if ($line === '!re') {
                if (!empty($current)) {
                    $users[] = $current;
                    $current = [];
                }
            } elseif (str_starts_with($line, '=') && str_contains($line, '=')) {
                $parts = explode('=', $line);
                if (count($parts) >= 3) {
                    $key = $parts[1];
                    $value = $parts[2];
                    $current[$key] = $value;
                }
            }
        }

        return $users;
    }

    public function kickUserById(string $id)
    {
        $this->write(['/ip/hotspot/active/remove', '.id=' . $id]);
        return $this->read();
    }

    public function getHotspotUsers(): array
    {
        $this->write(['/ip/hotspot/user/print']);
        $response = $this->read();
        
        Log::info('MikroTik Hotspot Users Raw Response:', ['response' => $response]);

        $users = [];
        $current = [];

        foreach ($response as $index => $line) {
            Log::info("Processing line {$index}: '{$line}'");
            
            if ($line === '!re') {
                // Start of new record - save previous if exists
                if (!empty($current)) {
                    $users[] = $current;
                    Log::info("Added user:", ['user' => $current]);
                }
                $current = []; // Reset for new record
            } elseif (str_starts_with($line, '=')) {
                // More robust parsing - find the second = sign
                $equalPos = strpos($line, '=', 1);
                if ($equalPos !== false) {
                    $key = substr($line, 1, $equalPos - 1);
                    $value = substr($line, $equalPos + 1);
                    $current[$key] = $value;
                    Log::info("Parsed key-value:", ['key' => $key, 'value' => $value]);
                }
            } elseif ($line === '!done') {
                // End of response - add last user if exists
                if (!empty($current)) {
                    $users[] = $current;
                    Log::info("Added final user:", ['user' => $current]);
                }
                Log::info("Command completed");
                break;
            }
        }

        // If no !done found, add the last user anyway
        if (!empty($current)) {
            $users[] = $current;
            Log::info("Added final user (no !done):", ['user' => $current]);
        }

        Log::info('Final Processed Hotspot Users:', ['users' => $users, 'count' => count($users)]);
        return $users;
    }

    public function addHotspotUser(string $name, string $password, string $profile = 'default', ?string $comment = null): array
    {
        $command = [
            '/ip/hotspot/user/add',
            '=name=' . $name,
            '=password=' . $password,
            '=profile=' . $profile
        ];

        if ($comment) {
            $command[] = '=comment=' . $comment;
        }

        $this->write($command);
        return $this->read();
    }

    public function updateHotspotUser(string $id, array $data): array
    {
        $command = ['/ip/hotspot/user/set', '.id=' . $id];
        
        foreach ($data as $key => $value) {
            $command[] = '=' . $key . '=' . $value;
        }

        $this->write($command);
        return $this->read();
    }

    public function deleteHotspotUser(string $id): array
    {
        $this->write(['/ip/hotspot/user/remove', '.id=' . $id]);
        return $this->read();
    }

    public function getHotspotProfiles(): array
    {
        $this->write(['/ip/hotspot/user/profile/print']);
        $response = $this->read();

        $profiles = [];
        $current = [];

        foreach ($response as $line) {
            if ($line === '!re') {
                if (!empty($current)) {
                    $profiles[] = $current;
                    $current = [];
                }
            } elseif (str_starts_with($line, '=') && str_contains($line, '=')) {
                $parts = explode('=', $line);
                if (count($parts) >= 3) {
                    $key = $parts[1];
                    $value = $parts[2];
                    $current[$key] = $value;
                }
            }
        }

        return $profiles;
    }

    // Hotspot Server Configuration Methods
    public function getHotspotServers(): array
    {
        $this->write(['/ip/hotspot/print']);
        $response = $this->read();

        $servers = [];
        $current = [];

        foreach ($response as $line) {
            if ($line === '!re') {
                if (!empty($current)) {
                    $servers[] = $current;
                    $current = [];
                }
            } elseif (str_starts_with($line, '=') && str_contains($line, '=')) {
                $parts = explode('=', $line);
                if (count($parts) >= 3) {
                    $key = $parts[1];
                    $value = $parts[2];
                    $current[$key] = $value;
                }
            }
        }

        return $servers;
    }

    public function enableHotspotServer(string $interface, string $profile = 'default'): array
    {
        $command = [
            '/ip/hotspot/add',
            '=name=' . $interface,
            '=interface=' . $interface,
            '=profile=' . $profile
        ];

        $this->write($command);
        return $this->read();
    }

    public function disableHotspotServer(string $id): array
    {
        $this->write(['/ip/hotspot/remove', '.id=' . $id]);
        return $this->read();
    }

    // Hotspot Server Profile Methods
    public function getHotspotServerProfiles(): array
    {
        $this->write(['/ip/hotspot/profile/print']);
        $response = $this->read();

        $profiles = [];
        $current = [];

        foreach ($response as $line) {
            if ($line === '!re') {
                if (!empty($current)) {
                    $profiles[] = $current;
                    $current = [];
                }
            } elseif (str_starts_with($line, '=') && str_contains($line, '=')) {
                $parts = explode('=', $line);
                if (count($parts) >= 3) {
                    $key = $parts[1];
                    $value = $parts[2];
                    $current[$key] = $value;
                }
            }
        }

        return $profiles;
    }

    public function updateHotspotServerProfile(string $id, array $data): array
    {
        $command = ['/ip/hotspot/profile/set', '.id=' . $id];
        
        foreach ($data as $key => $value) {
            $command[] = '=' . $key . '=' . $value;
        }

        $this->write($command);
        return $this->read();
    }

    // Login Page Design Methods
    public function getHotspotLoginPage(): array
    {
        $this->write(['/ip/hotspot/walled-garden/print']);
        $response = $this->read();

        $pages = [];
        $current = [];

        foreach ($response as $line) {
            if ($line === '!re') {
                if (!empty($current)) {
                    $pages[] = $current;
                    $current = [];
                }
            } elseif (str_starts_with($line, '=') && str_contains($line, '=')) {
                $parts = explode('=', $line);
                if (count($parts) >= 3) {
                    $key = $parts[1];
                    $value = $parts[2];
                    $current[$key] = $value;
                }
            }
        }

        return $pages;
    }

    public function setHotspotLoginPage(string $profileId, string $loginPage): array
    {
        $command = [
            '/ip/hotspot/profile/set',
            '.id=' . $profileId,
            '=login-page=' . $loginPage
        ];

        $this->write($command);
        return $this->read();
    }

    public function uploadHotspotFile(string $filename, string $content): array
    {
        // Note: File upload might need special handling depending on MikroTik version
        $command = [
            '/file/print',
            '?name=' . $filename
        ];

        $this->write($command);
        return $this->read();
    }

    public function testRawResponse(): array
    {
        $this->write(['/ip/hotspot/user/print']);
        return $this->read();
    }

    public function getSystemUsers(): array
    {
        $this->write(['/user/print']);
        $response = $this->read();
        
        Log::info('MikroTik System Users Raw Response:', ['response' => $response]);

        $users = [];
        $current = [];

        foreach ($response as $index => $line) {
            Log::info("Processing line {$index}: '{$line}'");
            
            if ($line === '!re') {
                if (!empty($current)) {
                    $users[] = $current;
                    Log::info("Added user:", ['user' => $current]);
                    $current = [];
                }
            } elseif (str_starts_with($line, '=')) {
                $equalPos = strpos($line, '=', 1);
                if ($equalPos !== false) {
                    $key = substr($line, 1, $equalPos - 1);
                    $value = substr($line, $equalPos + 1);
                    $current[$key] = $value;
                    Log::info("Parsed key-value:", ['key' => $key, 'value' => $value]);
                }
            } elseif ($line === '!done') {
                Log::info("Command completed");
                break;
            }
        }

        if (!empty($current)) {
            $users[] = $current;
            Log::info("Added final user:", ['user' => $current]);
        }

        Log::info('Final Processed System Users:', ['users' => $users, 'count' => count($users)]);
        return $users;
    }

    // Low-level Mikrotik binary communication ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

    protected function write(array $words): void
    {
        foreach ($words as $word) {
            $this->writeLength(strlen($word));
            fwrite($this->socket, $word);
        }
        fwrite($this->socket, chr(0)); // End of sentence
    }

    protected function read(): array
    {
        $response = [];
        while (true) {
            $len = $this->readLength();
            if ($len === 0) break;
            $response[] = fread($this->socket, $len);
        }
        return $response;
    }

    // Public methods for testing
    public function writePublic(array $words): void
    {
        $this->write($words);
    }

    public function readPublic(): array
    {
        return $this->read();
    }

    protected function writeLength(int $len): void
    {
        if ($len < 0x80) {
            fwrite($this->socket, chr($len));
        } elseif ($len < 0x4000) {
            $len |= 0x8000;
            fwrite($this->socket, chr(($len >> 8) & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        } elseif ($len < 0x200000) {
            $len |= 0xC00000;
            fwrite($this->socket, chr(($len >> 16) & 0xFF));
            fwrite($this->socket, chr(($len >> 8) & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        } elseif ($len < 0x10000000) {
            $len |= 0xE0000000;
            fwrite($this->socket, chr(($len >> 24) & 0xFF));
            fwrite($this->socket, chr(($len >> 16) & 0xFF));
            fwrite($this->socket, chr(($len >> 8) & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        } else {
            fwrite($this->socket, chr(0xF0));
            fwrite($this->socket, chr(($len >> 24) & 0xFF));
            fwrite($this->socket, chr(($len >> 16) & 0xFF));
            fwrite($this->socket, chr(($len >> 8) & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        }
    }

    protected function readLength(): int
    {
        $c = ord(fread($this->socket, 1));
        if ($c < 0x80) return $c;
        elseif (($c & 0xC0) === 0x80) return (($c & ~0xC0) << 8) + ord(fread($this->socket, 1));
        elseif (($c & 0xE0) === 0xC0) return (($c & ~0xE0) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
        elseif (($c & 0xF0) === 0xE0) return (($c & ~0xF0) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
        elseif (($c & 0xF8) === 0xF0) return (ord(fread($this->socket, 1)) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
        return 0;
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
        }
    }
}