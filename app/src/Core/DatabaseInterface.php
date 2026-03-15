<?php declare(strict_types=1);

namespace App\Core;

use PDO;

interface DatabaseInterface
{
    public function getConnection(): PDO;

    public function query(string $sql, array $params = []): \PDOStatement;

    public function execute(string $sql, array $params = []): bool;
}

