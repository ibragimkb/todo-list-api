<?php declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Config\FileConfig;
use App\Core\DatabaseInterface;
use Exception;
use PDO;
use PDOException;

class Mysql implements DatabaseInterface
{
    private static ?PDO $instance = null;

    public function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = new FileConfig(__DIR__ . '/../../config.php');

            $host = $config->get('db.host');
            $dbname = $config->get('db.dbname');
            $user = $config->get('db.user');
            $pass = $config->get('db.pass');

            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8";

            try {
                self::$instance = new PDO($dsn, $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                //die("Database connection error: " . $e->getMessage());
                throw new Exception('Database connection error');
            }
        }

        return self::$instance;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->getConnection()->prepare($sql);
        return $stmt->execute($params);
    }
}

