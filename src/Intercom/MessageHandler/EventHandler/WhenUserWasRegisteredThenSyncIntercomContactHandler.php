<?php

declare(strict_types=1);

namespace App\Intercom\MessageHandler\EventHandler;

use App\Intercom\IntercomClient;
use App\Repository\UserRepository;
use App\User\Message\Event\UserWasRegistered;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

//#[AsMessageHandler(bus: 'event.bus', fromTransport: 'async')]
class WhenUserWasRegisteredThenSyncIntercomContactHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly IntercomClient $intercomClient,
    ) {
    }

    public function __invoke(UserWasRegistered $message): void
    {
        try {
            $user = $this->userRepository->byId($message->getUserId());
        } catch (DomainException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), previous: $e);
        }

        $contact = $this->intercomClient->createUser($user);

        $user->setIntercomId($contact->id);
    }
}
