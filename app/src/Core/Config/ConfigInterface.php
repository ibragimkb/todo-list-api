<?php declare(strict_types=1);

namespace App\Core\Config;

interface ConfigInterface
{
    public function get(string $key, $default = null): mixed;
}

