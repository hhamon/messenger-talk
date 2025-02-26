<?php

declare(strict_types=1);

namespace App\User\MessageHandler;

use App\Repository\UserRepository;
use App\User\Message\SendWelcomeEmailMessage;
use App\User\UserEmailVerifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendWelcomeEmailMessageHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserEmailVerifier $userEmailVerifier,
    ) {
    }

    public function __invoke(SendWelcomeEmailMessage $message): void
    {
        $user = $this->userRepository->byId($message->getUserId());

        $this->userEmailVerifier->sendEmailConfirmation($user);
    }
}
