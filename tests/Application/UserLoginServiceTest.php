<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use PHPUnit\Framework\TestCase;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;

final class UserLoginServiceTest extends TestCase
{

    /**
     * @test
     */
    public function userAlreadyLoggedIn()
    {
        $user = new User("Asier");
        $userLoginService = new UserLoginService();

        $this->expectExceptionMessage("User already logged in");
        $userLoginService->manualLogin($user);
        $userLoginService->manualLogin($user);
    }

    /**
     * @test
     */
    public function userIsLoggedIn()
    {
        $user = new User("Asier");
        $userLoginService = new UserLoginService();

        $userLoginService->manualLogin($user);

        $this->assertEquals("Asier", $userLoginService->getLoggedUser($user));
    }

}
