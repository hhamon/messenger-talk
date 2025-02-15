<?php

declare(strict_types=1);

namespace App\Command;

use App\User\RegisterUser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:register-user',
    description: 'Register a new user.',
)]
final class RegisterUserCommand extends Command
{
    public function __construct(
        private readonly RegisterUser $registerUser,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address.')
            ->addArgument('gender', InputArgument::REQUIRED, 'The gender.')
            ->addArgument('full-name', InputArgument::REQUIRED, 'The full name.')
            ->addOption('country', null, InputOption::VALUE_REQUIRED, 'The residence country code.')
            ->addOption('birthdate', null, InputOption::VALUE_REQUIRED, 'The birthdate (YYYY-MM-DD).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var string $gender */
        $gender = $input->getArgument('gender');

        /** @var string $fullName */
        $fullName = $input->getArgument('full-name');

        /** @var string|null $country */
        $country = $input->getOption('country');

        /** @var string|null $country */
        $birthdate = $input->getOption('birthdate');

        /** @var string $plainPassword */
        $plainPassword = $io->askHidden('Password:');

        $user = $this->registerUser->registerUser(
            $email,
            $plainPassword,
            $gender,
            $fullName,
            $country,
            $birthdate,
        );

        $io->success(\sprintf('User %s was registered.', $user->getId()));

        return Command::SUCCESS;
    }
}
