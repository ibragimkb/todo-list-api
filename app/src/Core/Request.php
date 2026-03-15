<?php declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $headers = [];
    private array $query = [];
    private array $body = [];

    public function __construct()
    {
        $this->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri     = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $this->headers = $this->sanitizeArray(getallheaders() ?: []);
        $this->query   = $this->sanitizeArray($_GET ?? []);

        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);

        $this->body = $this->sanitizeArray(is_array($decoded) ? $decoded : []);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function header(string $key, $default = null)
    {
        return $this->headers[$key] ?? $default;
    }

    public function allHeaders(): array
    {
        return $this->headers;
    }

    public function query(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function allQuery(): array
    {
        return $this->query;
    }

    public function get(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->body;
    }

    private function sanitizeArray(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            $clean[$key] = is_array($value)
                ? $this->sanitizeArray($value)
                : $this->sanitizeValue($value);
        }

        return $clean;
    }

    private function sanitizeValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        // Trim whitespace
        $value = trim($value);

        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Prevent XSS
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Remove SQL injection patterns (basic normalization)
        $value = preg_replace('/(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|--|#)/i', '', $value);

        // Remove JS event handlers (onclick=, onload=, etc.)
        $value = preg_replace('/on\w+=/i', '', $value);

        // Remove javascript: pseudo-protocol
        $value = preg_replace('/javascript:/i', '', $value);

        return $value;
    }
}

