<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MikrotikServiceWrapper
{
    protected $mikrotikService;
    protected $isConnected = false;

    public function __construct()
    {
        // Don't initialize the actual service here
    }

    protected function getService()
    {
        if (!$this->mikrotikService) {
            // Only create the service when actually needed
            $this->mikrotikService = new MikrotikService();
        }
        return $this->mikrotikService;
    }

    public function __call($method, $arguments)
    {
        try {
            return $this->getService()->$method(...$arguments);
        } catch (\Exception $e) {
            Log::error("MikroTik service error: " . $e->getMessage());
            throw $e;
        }
    }
}
