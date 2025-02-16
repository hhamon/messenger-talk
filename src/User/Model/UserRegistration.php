<?php

declare(strict_types=1);

namespace App\User\Model;

use App\Entity\Gender;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Range;

#[UniqueEntity(
    fields: ['email'],
    message: 'This email is already taken.',
    entityClass: User::class,
)]
final class UserRegistration
{
    #[NotBlank]
    #[Email]
    private ?string $email = null;

    #[NotBlank]
    #[PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM)]
    private ?string $password = null;

    #[NotBlank]
    private ?Gender $gender = null;

    #[NotBlank]
    #[Length(min: 3, max: 150)]
    private ?string $fullName = null;

    #[Country]
    private ?string $country = null;

    #[Range(max: '-18 years')]
    private ?DateTimeImmutable $birthdate = null;

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = mb_strtolower($email);
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getFullName(): string
    {
        return (string) $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): void
    {
        $this->gender = $gender;
    }

    public function getBirthdate(): ?DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(?DateTimeImmutable $birthdate): void
    {
        $this->birthdate = $birthdate;
    }
}
