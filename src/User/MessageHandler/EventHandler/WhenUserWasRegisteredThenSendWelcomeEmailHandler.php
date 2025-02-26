<?php

declare(strict_types=1);

namespace App\User\MessageHandler\EventHandler;

use App\Repository\UserRepository;
use App\User\Message\Event\UserWasRegistered;
use App\User\UserEmailVerifier;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

//#[AsMessageHandler(fromTransport: 'async')]
final class WhenUserWasRegisteredThenSendWelcomeEmailHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserEmailVerifier $userEmailVerifier,
    ) {
    }

    public function __invoke(UserWasRegistered $message): void
    {
        try {
            $user = $this->userRepository->byId($message->getUserId());
        } catch (DomainException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), previous: $e);
        }

        $this->userEmailVerifier->sendEmailConfirmation($user);
    }
}
