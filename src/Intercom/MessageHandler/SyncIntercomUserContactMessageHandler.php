<?php

declare(strict_types=1);

namespace App\Intercom\MessageHandler;

use App\Intercom\IntercomClient;
use App\Intercom\Message\SyncIntercomUserContactMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

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
        $user = $this->userRepository->byId($message->getUserId());

        if ((string) $user->getIntercomId() !== '') {
            $this->intercomClient->updateUser($user);
            return;
        }

        $contact = $this->intercomClient->createUser($user);
        $user->setIntercomId($contact->id);
        $this->userRepository->save($user);
    }
}
