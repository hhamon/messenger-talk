<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use DomainException;
use Symfony\Component\Uid\Uuid;

interface UserRepository
{
    /**
     * @throws DomainException When user is not found
     */
    public function byId(Uuid $id): User;

    public function save(User $user): void;
}
