<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entité : User (Utilisateur)
 * Représente un utilisateur du système :
 * - Peut être client, propriétaire, employé ou administrateur.
 * - Contient les informations de profil, rôles et relations (réservations, messages, etc.).
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', columns: ['email'])
])]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN') or object == user"),
        new Post(security: "is_granted('PUBLIC_ACCESS')"),
        new Put(security: "is_granted('ROLE_ADMIN') or object == user"),
        new Patch(security: "is_granted('ROLE_ADMIN') or object == user"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'iexact',
    'firstname' => 'ipartial',
    'lastname' => 'ipartial',
    'roles' => 'exact',
    'isActive' => 'exact'
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** Identifiant unique */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    /** Email unique servant d'identifiant de connexion */
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Email]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    /** Rôles attribués à l'utilisateur (ROLE_USER, ROLE_OWNER, ROLE_EMPLOYEE, ROLE_ADMIN) */
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    /** Mot de passe haché de l'utilisateur */
    #[ORM\Column]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 8)]
    #[Groups(['user:write'])]
    private ?string $password = null;

    /** Prénom de l'utilisateur */
    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstname = null;

    /** Nom de famille de l'utilisateur */
    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastname = null;

    /** Sexe de l'utilisateur (true = homme, false = femme, null = non renseigné) */
    #[ORM\Column(nullable: true)]
    private ?bool $gender = null;

    /** Date de naissance */
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    /** Numéro de téléphone */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    /** Indique si l'email de l'utilisateur a été vérifié */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isEmailVerified = false;

    /** Indique si le compte est actif */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    /** Date de création du compte */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /** Réservations effectuées par le client */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'client')]
    private Collection $reservations;

    /** Espaces de coliving créés par le propriétaire */
    #[ORM\OneToMany(targetEntity: ColivingSpace::class, mappedBy: 'owner')]
    private Collection $colivingSpaces;

    /** Adresse principale associée à l'utilisateur */
    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Address $address = null;

    /** Photo de profil de l'utilisateur */
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Photo $photo = null;

    /** Messages envoyés */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messagesSent;

    /** Messages reçus */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $messagesReceived;

    /** Vérifications d'identité de l'utilisateur */
    #[ORM\OneToMany(targetEntity: VerificationUser::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $verificationUsers;

    /** Vérifications d'espaces effectuées par l'utilisateur (employé/admin) */
    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'user')]
    private Collection $userVerificationSpaces;

    /** Vérifications d'autres utilisateurs effectuées par cet utilisateur (employé/admin) */
    #[ORM\OneToMany(targetEntity: VerificationUser::class, mappedBy: 'owner')]
    private Collection $ownedVerifications;

    /** Constructeur : initialise les collections */
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->colivingSpaces = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->verificationUsers = new ArrayCollection();
        $this->userVerificationSpaces = new ArrayCollection();
        $this->ownedVerifications = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // === GETTERS & SETTERS ===

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getFirstname(): ?string { return $this->firstname; }
    public function setFirstname(string $firstname): static { $this->firstname = $firstname; return $this; }

    public function getLastname(): ?string { return $this->lastname; }
    public function setLastname(string $lastname): static { $this->lastname = $lastname; return $this; }

    public function getGender(): ?bool { return $this->gender; }
    public function setGender(?bool $gender): static { $this->gender = $gender; return $this; }

    public function getBirthDate(): ?\DateTimeInterface { return $this->birthDate; }
    public function setBirthDate(?\DateTimeInterface $birthDate): static { $this->birthDate = $birthDate; return $this; }

    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(?string $phoneNumber): static { $this->phoneNumber = $phoneNumber; return $this; }

    public function isEmailVerified(): bool { return $this->isEmailVerified; }
    public function setIsEmailVerified(bool $isEmailVerified): static { $this->isEmailVerified = $isEmailVerified; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** Retourne l'identifiant principal de l'utilisateur (email) */
    public function getUserIdentifier(): string { return (string) $this->email; }

    /** Efface les informations sensibles (non utilisées ici) */
    public function eraseCredentials(): void {}

    /**
     * Retourne l'adresse principale de l'utilisateur
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Définit l'adresse principale de l'utilisateur
     */
    public function setAddress(?Address $address): static
    {
        $this->address = $address;
        return $this;
    }
}
