<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ClientRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;

class SearchController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();

        // Capture search parameters
        $query = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $type  = isset($_GET['type']) && $_GET['type'] !== '' ? $_GET['type'] : 'all';
        $results = [];

        if ($query !== '') {
            // Base URL for building links
            $baseUrl = rtrim($this->config['app']['base_url'], '/');

            // Search clients
            if ($type === 'all' || $type === 'clients') {
                $clientRepo = new ClientRepository();
                $clients = $clientRepo->searchSimple($query, 10, null);
                foreach ($clients as $c) {
                    $title       = trim($c['business_name']) ?: trim($c['first_name'] . ' ' . $c['last_name']);
                    $description = $c['email'] ?? '';
                    $meta        = $c['phone'] ?? '';
                    $results[]   = [
                        'type'        => 'clients',
                        'title'       => $title,
                        'description' => $description,
                        'meta'        => $meta,
                        'url'         => $baseUrl . '/clients/' . $c['id'],
                    ];
                }
            }

            // Search sales
            if ($type === 'all' || $type === 'sales') {
                $saleRepo = new SaleRepository();
                $sales    = $saleRepo->searchSimple($query, 10);
                foreach ($sales as $s) {
                    $title       = $s['name'] ?? '';
                    $description = $s['note'] ?? '';
                    // Compose a simple meta string: type + start date (if available)
                    $metaParts = [];
                    if (!empty($s['type'])) {
                        $metaParts[] = ucfirst($s['type']);
                    }
                    if (!empty($s['start_date'])) {
                        $metaParts[] = $s['start_date'];
                    }
                    $meta = implode(' – ', $metaParts);
                    $results[] = [
                        'type'        => 'sales',
                        'title'       => $title,
                        'description' => $description,
                        'meta'        => $meta,
                        'url'         => $baseUrl . '/sales/' . $s['id'],
                    ];
                }
            }

            // Search users
            if ($type === 'all' || $type === 'users') {
                $userRepo = new UserRepository();
                $users    = $userRepo->searchSimple($query, 10);
                foreach ($users as $u) {
                    $title       = $u['email'] ?? '';
                    $description = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                    $meta        = $u['role'] ?? '';
                    $results[]   = [
                        'type'        => 'users',
                        'title'       => $title,
                        'description' => $description,
                        'meta'        => $meta,
                        // Link to edit page; adjust if a user show page is added later
                        'url'         => $baseUrl . '/admin/users/' . $u['id'] . '/edit',
                    ];
                }
            }
        }

        $this->render('search/index', [
            'title'   => ucfirst('search'),
            'user'    => $user,
            'results' => $results,
            'query'   => $query,
            'type'    => $type,
        ]);
    }

    // Additional actions (create/show/edit etc.) will be implemented iteratively.
}