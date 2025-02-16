<?php

declare(strict_types=1);

namespace App\User\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

#[AsMessage]
final readonly class SendWelcomeEmailMessage
{
    public function __construct(
        private string $userId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }
}
