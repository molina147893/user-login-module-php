<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Tests\Doubles\DummySessionManager;
use UserLoginService\Tests\Doubles\FakeSessionManager;
use UserLoginService\Tests\Doubles\MockSessionManager;
use UserLoginService\Tests\Doubles\SpySessionManager;
use UserLoginService\Tests\Doubles\StubSessionManager;

final class UserLoginServiceTest extends TestCase
{
    /**
     * @test
     */
    public function userIsLoggedInManually()
    {
        $user = new User('user_name');
        $expectedLoggedUsers = [$user];
        $userLoginService = new UserLoginService(new DummySessionManager());

        $userLoginService->manualLogin($user);

        $this->assertEquals($expectedLoggedUsers, $userLoginService->getLoggedUsers());
    }

    /**
     * @test
     */
    public function thereIsNoLoggedUser()
    {
        $userLoginService = new UserLoginService(new DummySessionManager());

        $this->assertEmpty($userLoginService->getLoggedUsers());
    }

    /**
     * @test
     */
    public function countsExternalSessions()
    {
        $userLoginService = new UserLoginService(new StubSessionManager());

        $externalsSessions = $userLoginService->countExternalSessions();

        $this->assertEquals(10, $externalsSessions);
    }

    /**
     * @test
     */
    public function userIsLoggedInExternalService()
    {
        $userName = 'user_name';
        $password = 'password';
        $userLoginService = new UserLoginService(new FakeSessionManager());

        $loginResponse = $userLoginService->login($userName, $password);

        $this->assertEquals(UserLoginService::LOGIN_CORRECTO, $loginResponse);
    }

    /**
     * @test
     */
    public function userIsNotLoggedInExternalService()
    {
        $userName = 'user_name';
        $password = 'wrong_password';
        $userLoginService = new UserLoginService(new FakeSessionManager());

        $loginResponse = $userLoginService->login($userName, $password);

        $this->assertEquals(UserLoginService::LOGIN_INCORRECTO, $loginResponse);
    }

    /**
     * @test
     **/
    public function userNotLoggedOutUserNotBeingLoggedIn()
    {
        $userLoginService = new UserLoginService(new DummySessionManager());
        $user = new User('user_name');

        $logoutResponse = $userLoginService->logout($user);

        $this->assertEquals(UserLoginService::USUARIO_NO_LOGEADO, $logoutResponse);
    }

    /**
     * @test
     **/
    public function userLogout()
    {
        $user = new User('user_name', 'password');
        $sessionManager = new SpySessionManager();
        $userLoginService = new UserLoginService($sessionManager);
        $userLoginService->manualLogin($user);

        $logoutResponse = $userLoginService->logout($user);

        $sessionManager->verifyLogoutCalls(1);
        $this->assertEquals('Ok', $logoutResponse);
    }

    /**
     * @test
     **/
    public function UserNotSecurelyLoggedInIfUserNotExistsInExternalService()
    {
        $user = new User('user_name', 'password');
        $sessionManager = new MockSessionManager();
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager->times(1);
        $sessionManager->withArguments('user_name');
        $sessionManager->andThrowException('User does not exist');

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertTrue($sessionManager->verifyValid());
        $this->assertEquals('Usuario no existe', $secureLoginResponse);
    }

    /**
     * @test
     **/
    public function UserNotSecurelyLoggedInIfCredentialsIncorrect()
    {
        $user = new User('user_name', 'password');
        $sesionManager = new MockSessionManager();
        $userLoginService = new UserLoginService($sesionManager);

        $sesionManager->times(1);
        $sesionManager->withArguments('user_name');
        $sesionManager->andThrowException('User incorrect credentials');

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertTrue($sesionManager->verifyValid());
        $this->assertEquals('Credenciales incorrectos', $secureLoginResponse);
    }

    /**
     * @test
     **/
    public function UserNotSecurelyLoggedInIfExternalServiceNotResponding()
    {
        $user = new User('user_name', 'password');
        $sesionManager = new MockSessionManager();
        $userLoginService = new UserLoginService($sesionManager);

        $sesionManager->times(1);
        $sesionManager->withArguments('user_name');
        $sesionManager->andThrowException('Service not responding');

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertTrue($sesionManager->verifyValid());
        $this->assertEquals('Servicio no responde', $secureLoginResponse);
    }

//    /**
//     * @test
//     */
//    public function userIsLoggedInManuallyMockery()
//    {
//        $user = new User('user_name');
//        $expectedLoggedUsers = [$user];
//        $userLoginService = new UserLoginService(Mockery::mock(SessionManager::class));
//
//        $userLoginService->manualLogin($user);
//
//        $this->assertEquals($expectedLoggedUsers, $userLoginService->getLoggedUsers());
//    }
//
//    /**
//     * @test
//     **/
//    public function UserNotSecurelyLoggedInIfUserNotExistsInExternalServiceMockery()
//    {
//        $user = new User('user_name', 'password');
//        $sessionManager = Mockery::mock(SessionManager::class);
//        $userLoginService = new UserLoginService($sessionManager);
//
//        $sessionManager
//            ->expects('secureLogin')
//            ->with('user_name')
//            ->once()
//            ->andThrow(new Exception('User does not exist'));
//
//        $secureLoginResponse = $userLoginService->secureLogin($user);
//
//        $this->assertEquals('Usuario no existe', $secureLoginResponse);
//    }
//
//    /**
//     * @test
//     **/
//    public function UserNotSecurelyLoggedInIfUserNotExistsInExternalServiceWithMockery()
//    {
//        $user = new User('user_name', 'password');
//        $sessionManager = \Mockery::mock(SessionManager::class);
//        $userLoginService = new UserLoginService($sessionManager);
//
//        $sessionManager
//            ->shouldReceive('secureLogin')
//            ->times(1)
//            ->with('user_name')
//            ->andThrow(new \Exception('User does not exist'));
//
//        $secureLoginResponse = $userLoginService->secureLogin($user);
//
//        $this->assertEquals('Usuario no existe', $secureLoginResponse);
//    }
}
