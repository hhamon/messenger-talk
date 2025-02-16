<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DoctrineUserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ORM\UniqueConstraint(name: 'user_email_unique', fields: ['email'])]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::TEXT)]
    private string $email;

    #[ORM\Column(type: Types::TEXT)]
    private string $password;

    #[ORM\Column(type: Types::TEXT, nullable: true, enumType: Gender::class)]
    private Gender $gender;

    #[ORM\Column(type: Types::TEXT)]
    private string $fullName;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intercomId = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $birthdate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $emailVerifiedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public static function register(
        string $email,
        string $password,
        string $gender,
        string $fullName,
        ?string $country = null,
        ?string $birthdate = null,
    ): self {
        return new self(
            Uuid::v7(),
            $email,
            $password,
            Gender::from($gender),
            $fullName,
            $country,
            $birthdate ? new DateTimeImmutable($birthdate) : null,
        );
    }

    public function __construct(
        Uuid $id,
        string $email,
        string $password,
        Gender $gender,
        string $fullName,
        ?string $country = null,
        ?DateTimeInterface $birthdate = null,
    ) {
        $this->id = $id;
        $this->email = mb_strtolower($email);
        $this->password = $password;
        $this->gender = $gender;
        $this->fullName = $fullName;
        $this->country = $country;
        $this->birthdate = $birthdate;
        $this->createdAt = new DatePoint();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): void
    {
        $this->gender = $gender;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): void
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

    public function getBirthdate(): ?DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?DateTimeInterface $birthdate): void
    {
        $this->birthdate = DateTimeImmutable::createFromInterface($birthdate);
    }

    public function getIntercomId(): ?string
    {
        return $this->intercomId;
    }

    public function setIntercomId(?string $intercomId): void
    {
        $this->intercomId = $intercomId;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getEmailVerifiedAt(): ?DateTimeInterface
    {
        return $this->emailVerifiedAt;
    }

    public function verify(?DateTimeImmutable $emailVerifiedAt = null): void
    {
        $this->emailVerifiedAt = $emailVerifiedAt ?? new DatePoint();
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt instanceof DateTimeInterface;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
