<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace UserLoginService\Application;

use Exception;
use UserLoginService\Domain\User;

class UserLoginService
{
    public const LOGIN_CORRECTO = 'Login correcto, gracias';
    public const LOGIN_INCORRECTO = 'Login incorrecto';
    public const USUARIO_NO_LOGEADO = 'Usuario no logeado';
    private $asdf;

    private SessionManager $sessionManager;

    /**
     * @var User[]
     */
    private array $loggedUsers = [];

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function manualLogin(User $user): void
    {
        $this->loggedUsers[] = $user;
    }

    public function getLoggedUsers(): array
    {
        return $this->loggedUsers;
    }

    public function countExternalSessions(): int
    {
        return $this->sessionManager->getSessions();
    }

    public function login(string $userName, string $password): string
    {
        if ($this->sessionManager->login($userName, $password)) {
            return self::LOGIN_CORRECTO;
        }

        return self::LOGIN_INCORRECTO;
    }

    public function logout(User $user): string
    {
        if (!\in_array($user, $this->getLoggedUsers(), true)) {
            return self::USUARIO_NO_LOGEADO;
        }

        $this->sessionManager->logout($user->getUserName());

        return 'Ok';
    }

    public function secureLogin(User $user)
    {
        try {
            $this->sessionManager->secureLogin($user->getUserName());
        } catch (Exception $exception) {
            if ('User does not exist' === $exception->getMessage()) {
                return 'Usuario no existe';
            }
            if ('User incorrect credentials' === $exception->getMessage()) {
                return 'Credenciales incorrectos';
            }
            if ('Service not responding' === $exception->getMessage()) {
                return 'Servicio no responde';
            }
        }

        return 'ok';
    }
}
