<?php
namespace App\Controllers;

use App\Core\Controller;

class UserController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->auth->user();
        $this->render('users/index', [
            'title' => ucfirst('users'),
            'user'  => $user,
        ]);
    }

    // Additional actions (create/show/edit etc.) will be implemented iteratively.
}