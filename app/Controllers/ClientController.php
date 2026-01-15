<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ClientRepository;

class ClientController extends Controller
{
    private ClientRepository $clients;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->clients = new ClientRepository();
    }

    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Existing filters structure used by clients/index.php
        $filters = [
            'q'           => $_GET['q']           ?? '',
            'active'      => $_GET['active']      ?? '1',   // '1', '0', or ''
            'client_type' => $_GET['client_type'] ?? '',
        ];

        // Paginated list using ClientRepository::paginate()
        $pagination = $this->clients->paginate($filters, $page, 25);

        $this->render('clients/index', [
            'title'      => 'Clients',
            'user'       => $user,
            'filters'    => $filters,
            'clients'    => $pagination['items'],
            'total'      => $pagination['total'],
            'current'    => $pagination['current'],
            'total_page' => $pagination['total_page'],
            'per_page'   => $pagination['per_page'],
        ]);
    }

    public function search(): void
    {
        // Global quick search (e.g. header search box)
        $this->requireAuth();
        header('Content-Type: application/json');

        $q = trim($_GET['q'] ?? '');
        if ($q === '') {
            echo json_encode([]);
            return;
        }

        // Use searchSimple(), default active = 1 (only active, non-deleted)
        $results = $this->clients->searchSimple($q, 10, 1);
        echo json_encode($results);
    }

    public function create(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();

        $this->render('clients/form', [
            'title'  => 'Add Client',
            'user'   => $user,
            'client' => null,
            'mode'   => 'create',
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if ($errors) {
            $user = $this->auth->user();
            $this->render('clients/form', [
                'title'  => 'Add Client',
                'user'   => $user,
                'client' => $data,
                'mode'   => 'create',
                'errors' => $errors,
            ]);
            return;
        }

        $id = $this->clients->create($data);
        $this->redirect('/clients/' . $id);
    }

    public function show(int $id): void
    {
        $this->requireAuth();
        $user   = $this->auth->user();
        $client = $this->clients->findById($id);

        if (!$client) {
            http_response_code(404);
            echo 'Client not found';
            return;
        }

        // Stubbed stats for now
        $stats = [
            'jobs_won'       => 0,
            'jobs_active'    => 0,
            'jobs_pending'   => 0,
            'jobs_cancelled' => 0,
            'total_gross'    => 0.0,
        ];
        $recentJobs = [];

        $this->render('clients/show', [
            'title'      => 'Client: ' . trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '')),
            'user'       => $user,
            'client'     => $client,
            'stats'      => $stats,
            'recentJobs' => $recentJobs,
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $user   = $this->auth->user();
        $client = $this->clients->findById($id);

        if (!$client) {
            http_response_code(404);
            echo 'Client not found';
            return;
        }

        $this->render('clients/form', [
            'title'  => 'Edit Client',
            'user'   => $user,
            'client' => $client,
            'mode'   => 'edit',
            'errors' => [],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        $existing = $this->clients->findById($id);
        if (!$existing) {
            http_response_code(404);
            echo 'Client not found';
            return;
        }

        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if ($errors) {
            $user = $this->auth->user();
            $this->render('clients/form', [
                'title'  => 'Edit Client',
                'user'   => $user,
                'client' => array_merge($existing, $data),
                'mode'   => 'edit',
                'errors' => $errors,
            ]);
            return;
        }

        $this->clients->update($id, $data);
        $this->redirect('/clients/' . $id);
    }

    public function deactivate(int $id): void
    {
        $this->requireAuth();
        $this->clients->softDelete($id);
        $this->redirect('/clients');
    }

    private function collectFormData(): array
    {
        return [
            'first_name'    => trim($_POST['first_name'] ?? ''),
            'last_name'     => trim($_POST['last_name'] ?? ''),
            'business_name' => trim($_POST['business_name'] ?? ''),
            'phone'         => trim($_POST['phone'] ?? ''),
            'can_text'      => isset($_POST['can_text']) ? 1 : 0,
            'email'         => trim($_POST['email'] ?? ''),
            'address_1'     => trim($_POST['address_1'] ?? ''),
            'address_2'     => trim($_POST['address_2'] ?? ''),
            'city'          => trim($_POST['city'] ?? ''),
            'state'         => trim($_POST['state'] ?? ''),
            'zip'           => trim($_POST['zip'] ?? ''),
            'client_type'   => $_POST['client_type'] ?? 'client',
            'note'          => trim($_POST['note'] ?? ''),
            // Always default new clients to active; edit form can override
            'active'        => isset($_POST['active']) ? 1 : 1,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['first_name'] === '' && $data['last_name'] === '' && $data['business_name'] === '') {
            $errors['name'] = 'Provide at least a name or business name.';
        }

        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        return $errors;
    }
}