<?php

declare(strict_types=1);

namespace App\Intercom\Model;

final readonly class IntercomContact
{
    /**
     * @param array{
     *   type: string,
     *   id: string,
     *   workspace_id: string,
     *   external_id: string,
     *   role: string,
     *   email: string,
     *   phone: string|null,
     *   name: string,
     *   created_at: int,
     *   updated_at: int,
     *   signed_up_at: int,
     * } $object
     */
    public static function fromArray(array $object): self
    {
        return new self($object['id']);
    }

    public function __construct(
        public string $id,
    ) {
    }
}
