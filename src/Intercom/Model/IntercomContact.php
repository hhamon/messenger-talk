<?php

declare(strict_types=1);

namespace App\Intercom\Model;

use Assert\Assertion;
use stdClass;

final readonly class IntercomContact
{
    public static function fromStdClass(stdClass $object): self
    {
        Assertion::propertyExists($object, 'id');
        Assertion::string($object->id);

        return new self($object->id);
    }

    public function __construct(
        public string $id,
    ) {
    }
}
