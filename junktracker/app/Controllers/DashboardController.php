<?php
namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();

        // Alpha stub: real metrics to be added next iteration
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user'  => $user,
        ]);
    }
}