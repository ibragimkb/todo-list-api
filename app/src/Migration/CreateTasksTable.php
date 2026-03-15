<?php declare(strict_types=1);

namespace App\Migration;

use App\Core\MigrationInterface;
use App\Core\DatabaseInterface;

class CreateTasksTable implements MigrationInterface
{
    public function up(DatabaseInterface $db): void
    {
        $db->execute("
CREATE TABLE `tasks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('pending', 'in_progress', 'done', 'archived') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    INDEX `idx_status` (`status`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_520_ci
        ");
    }

    public function down(DatabaseInterface $db): void
    {
        $db->exec("DROP TABLE IF EXISTS tasks;");
    }
}

