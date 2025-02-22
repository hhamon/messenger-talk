<?php

declare(strict_types=1);

namespace App\User\MessageHandler\CommandHandler;

use App\Entity\User;
use App\User\Message\Command\RegisterUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsMessageHandler(bus: 'command.bus')]
class RegisterUserHandler
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);

        $user = User::register(
            $command->getId(),
            $command->getEmail(),
            $passwordHasher->hash($command->getPassword()),
            $command->getGender(),
            $command->getFullName(),
            $command->getCountry(),
            $command->getBirthdate(),
        );

        $this->entityManager->persist($user);
    }
}
