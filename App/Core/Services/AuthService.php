<?php

namespace App\Core\Services;

use App\Core\DbModel;
use App\Core\Session;

class AuthService
{
    private Session $session;
    private string $userClass;
    private ?DbModel $user = null;

    public function __construct(Session $session, string $userClass)
    {
        $this->session = $session;
        $this->userClass = $userClass;
    }

    public function bootstrap(): void
    {
        $primaryValue = $this->session->get('user');

        if (!$primaryValue) {
            $this->user = null;
            return;
        }

        $primaryKey = $this->userClass::primaryKey();
        $user = $this->userClass::findOne([$primaryKey => $primaryValue]);

        if ($user instanceof DbModel) {
            $this->user = $user;
            return;
        }

        $this->session->remove('user');
        $this->user = null;
    }

    public function login(DbModel $user): void
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};

        $this->session->set('user', $primaryValue);
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public function getUser(): ?DbModel
    {
        return $this->user;
    }

    public function isGuest(): bool
    {
        return $this->user === null;
    }

}
