<?php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function showLoginForm(): void
    {
        if ($this->auth->user()) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error    = null;

        if ($email === '' || $password === '') {
            $error = 'Email and password are required.';
        } elseif (!$this->auth->attempt($email, $password)) {
            $error = 'Invalid credentials.';
        }

        if ($error) {
            $this->render('auth/login', [
                'title' => 'Login',
                'error' => $error,
            ]);
            return;
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('/login');
    }
}