<?php declare(strict_types=1);

namespace App\Core;

use App\Core\DatabaseInterface;

interface MigrationInterface
{
    public function up(DatabaseInterface $db): void;
    public function down(DatabaseInterface $db): void;
}

