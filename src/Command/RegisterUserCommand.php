<?php

declare(strict_types=1);

namespace App\Command;

use App\User\Message\Command\RegisterUser;
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
    name: 'app:register-user',
    description: 'Register a new user.',
)]
final class RegisterUserCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
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

        $id = Uuid::v7();

        $command = new RegisterUser(
            id: (string) $id,
            email: mb_strtolower($email),
            gender: $gender,
            fullName: $fullName,
            password: $plainPassword,
            country: $country,
            birthdate: $birthdate,
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (ValidationFailedException $e) {
            $io->warning('Validation failed.');
            $io->listing(\array_map(
                static fn (ConstraintViolation $violation): string => \sprintf('%s: %s', $violation->getPropertyPath(), $violation->getMessage()),
                \iterator_to_array($e->getViolations()),
            ));

            return self::FAILURE;
        }

        $io->success(\sprintf('User %s was registered.', $id));

        return Command::SUCCESS;
    }
}
