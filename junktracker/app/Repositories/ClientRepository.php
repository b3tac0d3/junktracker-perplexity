<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ClientRepository
{
    public function paginate(array $filters, int $page, int $perPage = 25): array
    {
        $pdo = Database::pdo();

        $where  = ['c.deleted_at IS NULL'];
        $params = [];

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $where[]          = 'c.active = :active';
            $params['active'] = (int)$filters['active']; // 1 or 0
        }

        // Client type
        if (!empty($filters['client_type'])) {
            $where[]               = 'c.client_type = :client_type';
            $params['client_type'] = $filters['client_type'];
        }

        // Search (name, business, phone, email)
        if (!empty($filters['q'])) {
            $q        = '%' . $filters['q'] . '%';
            $where[]  = '(c.first_name LIKE :q OR c.last_name LIKE :q OR c.business_name LIKE :q OR c.phone LIKE :q OR c.email LIKE :q)';
            $params['q'] = $q;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count
        $countSql = "SELECT COUNT(*) FROM clients c $whereSql";
        $stmt     = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total    = (int)$stmt->fetchColumn();

        $perPage = max(1, $perPage);
        $page    = max(1, $page);
        $offset  = ($page - 1) * $perPage;

        // Data
        $sql = "SELECT c.*
                FROM clients c
                $whereSql
                ORDER BY c.last_name, c.first_name
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'      => $rows,
            'total'      => $total,
            'per_page'   => $perPage,
            'current'    => $page,
            'total_page' => (int)ceil($total / $perPage),
        ];
    }

    public function searchSimple(string $q, int $limit = 10, ?int $active = 1): array
    {
        $pdo = Database::pdo();

        // $sql = '
        //     SELECT id, first_name, last_name, business_name, phone, city, state, active
        //     FROM clients
        //     WHERE deleted_at IS NULL
        // ';
        
        $sql = '
            SELECT id, first_name, last_name, business_name, phone, city, state, active
            FROM clients
            WHERE 1 = 1
        ';

        $params = [];

        // Only filter by active when explicitly passed (0 or 1)
        if ($active !== null) {
            $sql .= ' AND active = :active';
            $params[':active'] = $active;
        }

        $sql .= '
            AND (first_name LIKE :q 
                 OR last_name LIKE :q 
                 OR business_name LIKE :q 
                 OR phone LIKE :q 
                 OR email LIKE :q)
            ORDER BY last_name, first_name
            LIMIT :limit
        ';

        $stmt = $pdo->prepare($sql);

        $like = '%' . $q . '%';
        $stmt->bindValue(':q', $like);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if ($active !== null) {
            $stmt->bindValue(':active', $active, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $pdo = Database::pdo();
        $sql = 'INSERT INTO clients 
            (first_name, last_name, business_name, phone, can_text, email, address_1, address_2, city, state, zip, client_type, note, active, deleted_at)
            VALUES
            (:first_name, :last_name, :business_name, :phone, :can_text, :email, :address_1, :address_2, :city, :state, :zip, :client_type, :note, :active, NULL)';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'business_name' => $data['business_name'],
            'phone'         => $data['phone'],
            'can_text'      => $data['can_text'],
            'email'         => $data['email'],
            'address_1'     => $data['address_1'],
            'address_2'     => $data['address_2'],
            'city'          => $data['city'],
            'state'         => $data['state'],
            'zip'           => $data['zip'],
            'client_type'   => $data['client_type'],
            'note'          => $data['note'],
            'active'        => $data['active'] ?? 1,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $pdo = Database::pdo();
        $sql = 'UPDATE clients SET
            first_name = :first_name,
            last_name = :last_name,
            business_name = :business_name,
            phone = :phone,
            can_text = :can_text,
            email = :email,
            address_1 = :address_1,
            address_2 = :address_2,
            city = :city,
            state = :state,
            zip = :zip,
            client_type = :client_type,
            note = :note,
            active = :active
            WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id'            => $id,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'business_name' => $data['business_name'],
            'phone'         => $data['phone'],
            'can_text'      => $data['can_text'],
            'email'         => $data['email'],
            'address_1'     => $data['address_1'],
            'address_2'     => $data['address_2'],
            'city'          => $data['city'],
            'state'         => $data['state'],
            'zip'           => $data['zip'],
            'client_type'   => $data['client_type'],
            'note'          => $data['note'],
            'active'        => $data['active'] ?? 1,
        ]);
    }

    public function softDelete(int $id): void
    {
        $pdo = Database::pdo();
        $sql = 'UPDATE clients SET active = 0, deleted_at = NOW() WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
