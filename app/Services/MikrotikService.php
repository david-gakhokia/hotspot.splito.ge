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
}
