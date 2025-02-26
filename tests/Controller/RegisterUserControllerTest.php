<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\DoctrineUserRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Mailer\Test\InteractsWithMailer;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

final class RegisterUserControllerTest extends WebTestCase
{
    use HasBrowser;
    use InteractsWithMailer;
    use InteractsWithMessenger;
    use ResetDatabase;

    public function testRegisterUser(): void
    {
        $this->transport('async_priority_high')->queue()->assertEmpty();
        $this->transport('async_priority_low')->queue()->assertEmpty();

        $this->browser()
            ->followRedirects()
            ->visit('/registration')
            ->fillField('user_registration[gender]', 'male')
            ->fillField('user_registration[fullName]', 'John Smith')
            ->fillField('user_registration[birthdate]', '1978-02-26')
            ->fillField('user_registration[country]', 'US')
            ->fillField('user_registration[email]', 'jmsmith@example.com')
            ->fillField('user_registration[password][first]', 'jt/%Xq}EW"8`5?wmVxhN&;')
            ->fillField('user_registration[password][second]', 'jt/%Xq}EW"8`5?wmVxhN&;')
            ->click('Submit')
            ->assertOn('/registration/confirmation')
            ->assertSeeIn('h1', 'Signup Confirmation')
            ->assertSeeIn('.feedback', 'Welcome! Your registration is complete.');

        $this->transport('async_priority_high')->queue()->assertCount(1);
        $this->transport('async_priority_high')->processOrFail();

        $this->transport('async_priority_low')->queue()->assertCount(1);
        $this->transport('async_priority_low')->processOrFail();

        $this->mailer()
            ->assertSentEmailCount(1)
            ->assertEmailSentTo('jmsmith@example.com', 'Please Confirm your Email');

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'jmsmith@example.com']);

        self::assertSame(md5('jmsmith@example.com'), $user->getIntercomId());
    }
}
