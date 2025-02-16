<?php

declare(strict_types=1);

namespace App\Intercom;

use App\Entity\User;
use App\Intercom\Model\IntercomContact;

interface IntercomClient
{
    public function createUser(User $user): IntercomContact;

    public function updateUser(User $user): IntercomContact;
}
