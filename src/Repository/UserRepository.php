<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;

interface UserRepository
{
    public function byId(Uuid $id): User;

    public function save(User $user): void;
}
