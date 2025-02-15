<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\User;
use App\Repository\UserRepository;

class RegisterUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function registerUser(
        string $email,
        string $password,
        string $gender,
        string $fullName,
        ?string $country = null,
        ?string $birthdate = null,
    ): User {
        $user = User::register(
            $email,
            $password,
            $gender,
            $fullName,
            $country,
            $birthdate,
        );

        $this->userRepository->save($user);

        return $user;
    }
}
