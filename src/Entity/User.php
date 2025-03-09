<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NUMERO_TELEPHONE_PRINCIPAL', fields: ['numeroTelephonePrincipal'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $numeroTelephonePrincipal = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 50)]
    public string $type;

    #[ORM\Column(type: 'boolean')]
    public bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $ipAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $deviceFingerprint = null;

    #[ORM\Column(type: 'boolean')]
    public bool $discountUsed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroTelephonePrincipal(): ?string
    {
        return $this->numeroTelephonePrincipal;
    }

    public function setNumeroTelephonePrincipal(?string $numeroTelephonePrincipal): static
    {
        $this->numeroTelephonePrincipal = $numeroTelephonePrincipal;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->numeroTelephonePrincipal;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->numeroTelephonePrincipal ?? 'User';
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? $this->phoneNumber ?? '';
    }

}
