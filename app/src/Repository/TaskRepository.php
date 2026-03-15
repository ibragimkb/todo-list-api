<?php declare(strict_types=1);

namespace App\Repository;

use App\Core\DatabaseInterface;
use App\Core\Database\Mysql;
use PDO;

class TaskRepository
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db = null)
    {
        $this->db = $db ?? new Mysql();
    }

    public function getAllTasks(): array
    {
        $sql = "SELECT * FROM tasks ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);

        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        return $task ?: null;
    }

    public function create(string $title, ?string $description, string $status): int
    {
        $sql = "
            INSERT INTO tasks (title, description, status, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ";

        $this->db->execute($sql, [
            $title,
            $description,
            $status
        ]);

        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;

        $sql = "
            UPDATE tasks
            SET " . implode(', ', $fields) . ", updated_at = NOW()
            WHERE id = ?
        ";

        return $this->db->execute($sql, $values);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tasks WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}

