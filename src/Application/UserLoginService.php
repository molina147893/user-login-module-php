<?php

namespace UserLoginService\Application;

use Exception;
use UserLoginService\Domain\User;

class UserLoginService
{
    private array $loggedUsers = [];

    public function manualLogin(User $user): void
    {
        throw new Exception("User already logged in");
    }

}