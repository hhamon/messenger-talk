<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\Gender;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeInterface;

class RegisterUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function registerUser(
        string $email,
        string $password,
        Gender|string $gender,
        string $fullName,
        ?string $country = null,
        DateTimeInterface|string|null $birthdate = null,
    ): User {
        if ($birthdate instanceof DateTimeInterface) {
            $birthdate = $birthdate->format('Y-m-d');
        }

        if ($gender instanceof Gender) {
            $gender = $gender->value;
        }

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
