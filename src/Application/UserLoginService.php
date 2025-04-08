<?php

namespace UserLoginService\Application;

use Exception;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

class UserLoginService
{
    private array $loggedUsers = [];

    public function __construct(private SessionManager $sessionManager)
    {
    }

    public function manualLogin(User $user): void
    {
        if (in_array($user->getUserName(), $this->loggedUsers)) {
            throw new Exception("User already logged in");
        }

        $this->loggedUsers[] = $user->getUserName();
    }

    public function logout(User $user): string
    {
        if(in_array($user->getUserName(), $this->loggedUsers)) {
            return("Ok");
        }
        return("User not found");
    }

    public function getLoggedUser(User $user): string
    {
        return $user->getUserName();
    }

    public function getExternalSession(): int
    {
        return $this->sessionManager->getSessions();
    }

}