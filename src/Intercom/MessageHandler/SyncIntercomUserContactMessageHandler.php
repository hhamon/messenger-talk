<?php

declare(strict_types=1);

namespace App\Intercom\MessageHandler;

use App\Intercom\IntercomClient;
use App\Intercom\Message\SyncIntercomUserContactMessage;
use App\Repository\UserRepository;
use DomainException;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
final class SyncIntercomUserContactMessageHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly IntercomClient $intercomClient,
    ) {
    }

    public function __invoke(SyncIntercomUserContactMessage $message): void
    {
        try {
            $user = $this->userRepository->byId($message->getUserId());
        } catch (DomainException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), previous: $e);
        }

        if ((string) $user->getIntercomId() !== '') {
            $this->intercomClient->updateUser($user);
            return;
        }

        try {
            $contact = $this->intercomClient->createUser($user);
        } catch (ClientExceptionInterface $e) {
            // In case of a network outage, or when HTTP request failed, then always force retrying
            throw new RecoverableMessageHandlingException($e->getMessage(), previous: $e, retryDelay: 6000);
        }

        $user->setIntercomId($contact->id);
    }
}
