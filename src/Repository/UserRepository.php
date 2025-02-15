<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepository
{
    public function save(User $user): void;
}
