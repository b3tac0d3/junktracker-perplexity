<?php
namespace App\Controllers;

use App\Core\Controller;

class SearchController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();
        $this->render('search/index', [
            'title' => ucfirst('search'),
            'user'  => $user,
        ]);
    }

    // Additional actions (create/show/edit etc.) will be implemented iteratively.
}