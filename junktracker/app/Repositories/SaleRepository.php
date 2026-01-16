<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class SaleRepository
{
    /**
     * Perform a simple search on sales by name or note.
     *
     * This helper executes a case-insensitive search against the sales
     * name and note columns and returns at most $limit rows ordered by
     * start_date descending. It can be used for global search queries.
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
            SELECT s.*
            FROM sales s
            WHERE s.name LIKE :q OR s.note LIKE :q
            ORDER BY s.start_date DESC, s.id DESC
            LIMIT :limit
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q', $query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function paginate(array $filters, int $page, int $perPage = 25): array
    {
        $pdo = Database::pdo();

        $where  = [];
        $params = [];

        // Type filter (shop/scrap/ebay/other)
        if (!empty($filters['type'])) {
            $where[]         = 's.type = :type';
            $params['type']  = $filters['type'];
        }

        // Search (name, note)
        if (!empty($filters['q'])) {
            $q       = '%' . $filters['q'] . '%';
            $where[] = '(s.name LIKE :q OR s.note LIKE :q)';
            $params['q'] = $q;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count
        $countSql = "
            SELECT COUNT(*)
            FROM sales s
            $whereSql
        ";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $perPage = max(1, $perPage);
        $page    = max(1, $page);
        $offset  = ($page - 1) * $perPage;

        // Data with optional job + client join
        $sql = "
            SELECT 
                s.*,
                j.name as job_name,
                COALESCE(
                    c.business_name,
                    CONCAT_WS(' ', c.first_name, c.last_name)
                ) as client_name
            FROM sales s
            LEFT JOIN jobs j ON s.job_id = j.id
            LEFT JOIN clients c ON j.client_id = c.id
            $whereSql
            ORDER BY s.start_date DESC, s.id DESC
            LIMIT :limit OFFSET :offset
        ";

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

    public function findById(int $id): ?array
    {
        $pdo = Database::pdo();

        $sql = "
            SELECT 
                s.*,
                j.name as job_name,
                c.first_name,
                c.last_name,
                c.business_name,
                c.phone,
                c.email
            FROM sales s
            LEFT JOIN jobs j ON s.job_id = j.id
            LEFT JOIN clients c ON j.client_id = c.id
            WHERE s.id = :id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(array $data): int
    {
        $pdo = Database::pdo();

        $sql = '
            INSERT INTO sales 
            (job_id, type, name, note, start_date, end_date, gross_amount, net_amount, active, created_at)
            VALUES
            (:job_id, :type, :name, :note, :start_date, :end_date, :gross_amount, :net_amount, :active, NOW())
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'job_id'       => $data['job_id'] ?? null,
            'type'         => $data['type'],
            'name'         => $data['name'],
            'note'         => $data['note'] ?? null,
            'start_date'   => $data['start_date'] ?? null,
            'end_date'     => $data['end_date'] ?? null,
            'gross_amount' => $data['gross_amount'],
            'net_amount'   => $data['net_amount'] ?? null,
            'active'       => $data['active'] ?? 1,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $pdo = Database::pdo();

        $sql = '
            UPDATE sales SET
                job_id = :job_id,
                type = :type,
                name = :name,
                note = :note,
                start_date = :start_date,
                end_date = :end_date,
                gross_amount = :gross_amount,
                net_amount = :net_amount,
                active = :active,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id'           => $id,
            'job_id'       => $data['job_id'] ?? null,
            'type'         => $data['type'],
            'name'         => $data['name'],
            'note'         => $data['note'] ?? null,
            'start_date'   => $data['start_date'] ?? null,
            'end_date'     => $data['end_date'] ?? null,
            'gross_amount' => $data['gross_amount'],
            'net_amount'   => $data['net_amount'] ?? null,
            'active'       => $data['active'] ?? 1,
        ]);
    }

    public function delete(int $id): void
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare('UPDATE sales SET deleted_at = NOW(), active = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
