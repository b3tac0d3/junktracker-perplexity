<?php
namespace App\Core;

use App\Repositories\UserRepository;

class Auth
{
    private UserRepository $users;

    public function __construct(array $config)
    {
        $this->users = new UserRepository();
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if (!$user || !$user['is_active']) {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
    }

    public function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        return $this->users->findById((int)$_SESSION['user_id']);
    }
}