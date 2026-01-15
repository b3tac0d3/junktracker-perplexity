<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\SaleRepository;

class SaleController extends Controller
{
    private SaleRepository $sales;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->sales = new SaleRepository();
    }

    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $filters = [
            'q'      => $_GET['q']      ?? '',
            'status' => $_GET['status'] ?? '',
            'source' => $_GET['source'] ?? '',
        ];

        $pagination = $this->sales->paginate($filters, $page, 25);

        $this->render('sales/index', [
            'title'   => 'Sales',
            'user'    => $user,
            'filters' => $filters,
            'sales'   => $pagination['items'],
            'pager'   => $pagination,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();

        $this->render('sales/form', [
            'title' => 'Add Sale',
            'user'  => $user,
            'sale'  => null,
            'mode'  => 'create',
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
            $this->render('sales/form', [
                'title'  => 'Add Sale',
                'user'   => $user,
                'sale'   => $data,
                'mode'   => 'create',
                'errors' => $errors,
            ]);
            return;
        }

        $id = $this->sales->create($data);
        $this->redirect('/sales/' . $id);
    }

    public function show(int $id): void
    {
        $this->requireAuth();
        $user = $this->auth->user();
        $sale = $this->sales->findById($id);

        if (!$sale) {
            http_response_code(404);
            echo 'Sale not found';
            return;
        }

        $this->render('sales/show', [
            'title' => 'Sale Details',
            'user'  => $user,
            'sale'  => $sale,
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $user = $this->auth->user();
        $sale = $this->sales->findById($id);

        if (!$sale) {
            http_response_code(404);
            echo 'Sale not found';
            return;
        }

        $this->render('sales/form', [
            'title'  => 'Edit Sale',
            'user'   => $user,
            'sale'   => $sale,
            'mode'   => 'edit',
            'errors' => [],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        $existing = $this->sales->findById($id);
        if (!$existing) {
            http_response_code(404);
            echo 'Sale not found';
            return;
        }

        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if ($errors) {
            $user = $this->auth->user();
            $this->render('sales/form', [
                'title' => 'Edit Sale',
                'user'  => $user,
                'sale'  => array_merge($existing, $data),
                'mode'  => 'edit',
                'errors' => $errors,
            ]);
            return;
        }

        $this->sales->update($id, $data);
        $this->redirect('/sales/' . $id);
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->sales->delete($id);
        $this->redirect('/sales');
    }

    private function collectFormData(): array
    {
        return [
            'client_id' => !empty($_POST['client_id']) ? (int)$_POST['client_id'] : null,
            'sale_date' => trim($_POST['sale_date'] ?? ''),
            'amount'    => trim($_POST['amount'] ?? ''),
            'status'    => $_POST['status'] ?? 'pending',
            'source'    => $_POST['source'] ?? 'shop',
            'note'      => trim($_POST['note'] ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['sale_date'])) {
            $errors['sale_date'] = 'Sale date is required.';
        }

        if (empty($data['amount']) || !is_numeric($data['amount'])) {
            $errors['amount'] = 'Valid amount is required.';
        }

        if (empty($data['status'])) {
            $errors['status'] = 'Status is required.';
        }

        if (empty($data['source'])) {
            $errors['source'] = 'Source is required.';
        }

        return $errors;
    }
}