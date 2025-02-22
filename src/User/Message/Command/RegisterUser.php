<?php

declare(strict_types=1);

namespace App\User\Message\Command;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[AsMessage]
#[UniqueEntity(fields: ['email'], entityClass: User::class, errorPath: 'email')]
final readonly class RegisterUser
{
    public function __construct(
        private string $id,
        #[NotBlank]
        #[Email]
        private string $email,
        #[NotBlank]
        #[Choice(choices: ['male', 'female', 'other'])]
        private string $gender,
        #[NotBlank]
        #[Length(min: 2, max: 255)]
        private string $fullName,
        #[NotBlank]
        #[PasswordStrength(minScore: PasswordStrength::STRENGTH_STRONG)]
        private string $password,
        #[Country]
        private ?string $country = null,
        #[Date]
        private ?string $birthdate = null,
    ) {
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }
}
