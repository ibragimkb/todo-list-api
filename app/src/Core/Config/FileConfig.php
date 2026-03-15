<?php declare(strict_types=1);

namespace App\Core\Config;

class FileConfig implements ConfigInterface
{
    private array $config = [];

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Config file not found: $path");
        }

        $this->config = require $path;
    }

    public function get(string $key, $default = null): mixed
    {
        $parts = explode('.', $key);
        $value = $this->config;

        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }
}

