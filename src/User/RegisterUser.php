<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\Gender;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class RegisterUser
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
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
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);

        if ($birthdate instanceof DateTimeInterface) {
            $birthdate = $birthdate->format('Y-m-d');
        }

        if ($gender instanceof Gender) {
            $gender = $gender->value;
        }

        $user = User::register(
            $email,
            $passwordHasher->hash($password),
            $gender,
            $fullName,
            $country,
            $birthdate,
        );

        $this->userRepository->save($user);

        return $user;
    }
}
