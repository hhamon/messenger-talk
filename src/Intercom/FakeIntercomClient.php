<?php

declare(strict_types=1);

namespace App\Intercom;

use App\Entity\User;
use App\Intercom\Model\IntercomContact;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('test')]
class FakeIntercomClient implements IntercomClient
{
    public function createUser(User $user): IntercomContact
    {
        return $this->syncContact($user);
    }

    public function updateUser(User $user): IntercomContact
    {
        return $this->syncContact($user);
    }

    private function syncContact(User $user): IntercomContact
    {
        return new IntercomContact(md5($user->getEmail()));
    }
}
