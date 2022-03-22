<?php

namespace UserLoginService\Tests\Doubles;

use UserLoginService\Application\SessionManager;

class DummySessionManager implements SessionManager
{
    public function getSessions(): int
    {
    }

    public function login(string $userName, string $password): bool
    {
    }

    public function logout(string $getUserName): string
    {
        // TODO: Implement logout() method.
    }

    public function secureLogin(string $getUserName)
    {
        // TODO: Implement secureLogin() method.
    }
}
