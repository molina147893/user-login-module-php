<?php

namespace UserLoginService\Tests\Doubles;

use Exception;
use UserLoginService\Application\SessionManager;

class SpySessionManager implements SessionManager
{
    private int $calls = 0;

    public function getSessions(): int
    {
    }

    public function login(string $userName, string $password): bool
    {
    }

    public function logout(string $getUserName)
    {
        $this->calls++;
    }

    public function verifyLogoutCalls(int $expectedCalls): bool
    {
        if($this->calls !== $expectedCalls){
            throw new Exception('Logout calls incorrect');
        }

        return true;
    }

    public function secureLogin(string $getUserName)
    {
        // TODO: Implement secureLogin() method.
    }
}