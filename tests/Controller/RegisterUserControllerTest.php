<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class RegisterUserControllerTest extends WebTestCase
{
    public function testRegisterUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/registration');

        self::assertResponseIsSuccessful();

        $client->submitForm('Submit', [
            'user_registration[gender]' => 'male',
            'user_registration[fullName]' => 'John Smith',
            'user_registration[birthdate]' => '1978-02-26',
            'user_registration[country]' => 'US',
            'user_registration[email]' => 'jmsmith@example.com',
            'user_registration[password][first]' => 'jt/%Xq}EW"8`5?wmVxhN&;',
            'user_registration[password][second]' => 'jt/%Xq}EW"8`5?wmVxhN&;',
        ]);

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $transport->getSent());

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(1, $transport->getSent());

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Signup Confirmation');
        self::assertSelectorTextSame('.feedback', 'Welcome! Your registration is complete.');
    }
}
