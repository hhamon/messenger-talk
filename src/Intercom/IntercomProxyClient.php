<?php

declare(strict_types=1);

namespace App\Intercom;

use App\Entity\User;
use App\Intercom\Model\IntercomContact;
use Intercom\IntercomClient as IntercomSdkClient;
use Intercom\IntercomContacts;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Autoconfigure(
    bind: [
        '$intercomAuthToken' => '%env(INTERCOM_API_TOKEN)%',
        '$intercomClient' => '@intercom.client',
    ],
    constructor: 'create',
)]
final class IntercomProxyClient implements IntercomClient
{
    public static function create(string $intercomAuthToken, HttpClientInterface $intercomClient): self
    {
        $intercom = new IntercomSdkClient($intercomAuthToken);
        $intercom->setHttpClient(new Psr18Client($intercomClient));

        return new self($intercom->contacts);
    }

    public function __construct(
        private readonly IntercomContacts $intercomContacts,
    ) {
    }

    public function createUser(User $user): IntercomContact
    {
        $contact = $this->intercomContacts->create($this->getUserContactPayload($user));

        return IntercomContact::fromArray((array) $contact);
    }

    public function updateUser(User $user): IntercomContact
    {
        $intercomId = (string) $user->getIntercomId();

        if ($intercomId === '') {
            throw new RuntimeException('User does not have an Intercom ID.');
        }

        $contact = $this->intercomContacts->update($intercomId, $this->getUserContactPayload($user));

        return IntercomContact::fromArray((array) $contact);
    }

    private function getUserContactPayload(User $user): array
    {
        return [
            'type' => 'user',
            'external_id' => (string) $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getFullName(),
            'signed_up_at' => $user->getCreatedAt()->getTimestamp(),
            'custom_attributes' => [
                'gender' => $user->getGender()->value,
                'birthdate' => $user->getBirthdate()?->format('Y-m-d'),
                'country' => $user->getCountry(),
                'email_verified_at' => $user->getEmailVerifiedAt()?->getTimestamp(),
            ],
        ];
    }
}
