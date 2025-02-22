<?php

declare(strict_types=1);

namespace App\User\MessageHandler\CommandHandler;

use App\Entity\User;
use App\Intercom\Message\SyncIntercomUserContactMessage;
use App\User\Message\Command\RegisterUser;
use App\User\Message\SendWelcomeEmailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsMessageHandler(bus: 'messenger.bus.default')]
class RegisterUserHandler
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
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

        $this->messageBus->dispatch(
            new SendWelcomeEmailMessage((string) $user->getId()),
            [new DispatchAfterCurrentBusStamp()],
        );

        $this->messageBus->dispatch(
            new SyncIntercomUserContactMessage((string) $user->getId()),
            [new DispatchAfterCurrentBusStamp()],
        );
    }
}
