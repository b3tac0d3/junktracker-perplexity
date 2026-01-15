<?php
namespace App\Controllers;

use App\Core\Controller;

class JobController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();
        $this->render('jobs/index', [
            'title' => ucfirst('jobs'),
            'user'  => $user,
        ]);
    }

    // Additional actions (create/show/edit etc.) will be implemented iteratively.
}