<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use PHPUnit\Framework\TestCase;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

use Mockery;

final class UserLoginServiceTest extends TestCase
{

    /**
     * @test
     */
    public function userAlreadyLoggedIn()
    {
        $user = new User("Asier");
        $facebookSessionManager = new FacebookSessionManager();
        $userLoginService = new UserLoginService($facebookSessionManager);

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
        $facebookSessionManager = new FacebookSessionManager();
        $userLoginService = new UserLoginService($facebookSessionManager);

        $userLoginService->manualLogin($user);

        $this->assertEquals("Asier", $userLoginService->getLoggedUser($user));
    }

    /**
     * @test
     */
    public function getNumberOfSession()
    {
        // Creame el doble de la siguiente clase que te voy a pasar, dummy de FacebookSessionManager
        $facebookSessionManager = Mockery::mock(FacebookSessionManager::class);
        $userLoginService = new UserLoginService($facebookSessionManager);

        $facebookSessionManager->allows()->getSessions()->andReturn(4);


        $this->assertEquals(4, $userLoginService->getExternalSession());
    }

    /**
     * @test
     */
    public function userIsLoggedOut()
    {
        $user = new User("Asier");
        $facebookSessionManager = Mockery::mock(FacebookSessionManager::class);
        $userLoginService = new UserLoginService($facebookSessionManager);

        $facebookSessionManager->allows()->logout("Asier")->andReturn(true);

        $this->assertEquals("Ok", $userLoginService->logout($user));
    }
}
