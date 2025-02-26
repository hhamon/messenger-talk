<?php

declare(strict_types=1);

namespace App\Command;

use App\User\Message\Command\RegisterUser;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolation;

#[AsCommand(
    name: 'app:register-many-users',
    description: 'Register many users.',
)]
final class RegisterUsersCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of users to register.', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $faker = Factory::create('en');

        $limit = (int) $input->getOption('limit');

        while ($limit > 0) {
            $gender = $faker->randomElement(['male', 'female', 'other']);
            $fakerGender = $gender !== 'other' ? $gender : null;
            $birthdate = $faker->boolean(60) ? $faker->dateTimeBetween('-100 years', '-18 years') : null;

            $this->messageBus->dispatch(
                new RegisterUser(
                    id: (string) Uuid::v7(),
                    email: mb_strtolower($faker->unique()->safeEmail()),
                    gender: $gender,
                    fullName: $faker->firstName($fakerGender) . ' ' . $faker->lastName($fakerGender),
                    password: $faker->password(16, 32),
                    country: $faker->boolean(75) ? $faker->countryCode : null,
                    birthdate: $birthdate?->format('Y-m-d'),
                ),
            );

            $limit--;
        }

        $io->success('Done');

        return Command::SUCCESS;
    }
}
