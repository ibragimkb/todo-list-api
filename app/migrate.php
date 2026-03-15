<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Migrator;
use App\Migration\CreateTasksTable;

$migrator = new Migrator();

$migrator->addMigration('create_tasks_table', new CreateTasksTable());

$migrator->run();

printf("Migrations executed.\n");

