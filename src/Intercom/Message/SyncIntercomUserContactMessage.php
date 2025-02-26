<?php

declare(strict_types=1);

namespace App\Intercom\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Messenger\Monitor\Stamp\TagStamp;

#[AsMessage('async_priority_low')]
#[TagStamp('marketing')]
#[TagStamp('intercom')]
final readonly class SyncIntercomUserContactMessage
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
