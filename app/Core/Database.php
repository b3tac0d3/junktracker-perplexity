<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function init(array $config): void
    {
        if (self::$pdo !== null) {
            return;
        }
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset'] ?? 'utf8mb4'
        );
        self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    public static function pdo(): PDO
    {
        if (!self::$pdo) {
            throw new PDOException('Database not initialized');
        }
        return self::$pdo;
    }
}