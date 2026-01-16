<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function all(): array
    {
        $stmt = Database::pdo()->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (email, first_name, last_name, role, password_hash, is_active)
             VALUES (:email, :first_name, :last_name, :role, :password_hash, :is_active)'
        );
        $stmt->execute([
            'email'         => $data['email'],
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'role'          => $data['role'],
            'password_hash' => $data['password_hash'],
            'is_active'     => $data['is_active'] ?? 1,
        ]);
        return (int)Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = ['id' => $id];
        foreach (['email','first_name','last_name','role','password_hash','is_active'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[]           = "$field = :$field";
                $params[$field]     = $data[$field];
            }
        }
        if (!$fields) {
            return;
        }
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Perform a simple search on users by email or name.
     *
     * @param string $q     Search query
     * @param int    $limit Maximum number of records to return
     * @return array
     */
    public function searchSimple(string $q, int $limit = 10): array
    {
        $pdo   = Database::pdo();
        $query = '%' . $q . '%';
        $sql   = '
            SELECT *
            FROM users
            WHERE email LIKE :q OR first_name LIKE :q OR last_name LIKE :q
            ORDER BY last_name, first_name
            LIMIT :limit
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q', $query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}