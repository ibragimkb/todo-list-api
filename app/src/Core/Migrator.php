<?php declare(strict_types=1);

namespace App\Core;

use App\Core\DatabaseInterface;
use App\Core\Database\Mysql;
use PDO;

class Migrator
{
    private DatabaseInterface $db;
    private array $migrations = [];

    public function __construct(DatabaseInterface $db = null)
    {
        $this->db = $db ?? new Mysql();
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $this->db->execute("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function addMigration(string $name, MigrationInterface $migration): void
    {
        $this->migrations[$name] = $migration;
    }

    public function run(): void
    {
        foreach ($this->migrations as $name => $migration) {
            if ($this->isExecuted($name)) {
                continue;
            }

            $migration->up($this->db);
            $this->markExecuted($name);
        }
    }

    private function isExecuted(string $name): bool
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM migrations WHERE migration = ?", [$name]);
        return (bool)$stmt->fetchColumn();
    }

    private function markExecuted(string $name): void
    {
        $this->db->execute("INSERT INTO migrations (migration) VALUES (?)", [$name]);
    }
}

