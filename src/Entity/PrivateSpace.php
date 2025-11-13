<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\PrivateSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Controllers
use App\Controller\PublishPrivateSpaceController;
use App\Controller\SuspendPrivateSpaceController;

// Serializer
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Espace privé
 */
#[ORM\Entity(repositoryClass: PrivateSpaceRepository::class)]
#[ORM\Table(name: 'private_space')]
#[ApiResource(
    normalizationContext: ['groups' => ['private:read']],
    denormalizationContext: ['groups' => ['private:write']],
    operations: [
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Get(security: "is_granted('PUBLIC_ACCESS')"),

        new Post(
            security: "is_granted('ROLE_OWNER')",
            securityMessage: "Seuls les propriétaires peuvent créer un espace privé."
        ),

        new Put(
            security: "is_granted('ROLE_OWNER') and object.getColivingSpace().getOwner() == user and object.getIsActive() == false"
        ),
        new Patch(
            security: "is_granted('ROLE_OWNER') and object.getColivingSpace().getOwner() == user and object.getIsActive() == false"
        ),

        new Patch(
            uriTemplate: '/private_spaces/{id}/publish',
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            input: false,
            output: false,
            controller: PublishPrivateSpaceController::class
        ),

        new Patch(
            uriTemplate: '/private_spaces/{id}/suspend',
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            input: false,
            output: false,
            controller: SuspendPrivateSpaceController::class
        ),

        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'colivingSpace.id' => 'exact',
    'capacity' => 'exact',
    'pricePerMonth' => 'exact',
    'isActive' => 'exact'
])]
class PrivateSpace
{
    #[Groups(['private:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\Column(length: 50)]
    private ?string $titlePrivateSpace = null;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionPrivateSpace = null;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\Column]
    private ?int $capacity = null;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $areaM2 = null;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $pricePerMonth = null;

    #[Groups(['private:read'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\Column(options: ['default' => false])]
    private ?bool $isActive = false;

    #[Groups(['private:read', 'private:write'])]
    #[ORM\ManyToOne(inversedBy: 'privateSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'privateSpace')]
    private Collection $reservations;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'privateSpace')]
    private Collection $photos;

    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'privateSpace')]
    private Collection $verificationSpaces;

    #[ORM\ManyToMany(targetEntity: Amenity::class, inversedBy: 'privateSpaces')]
    private Collection $amenities;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->amenities = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- GETTERS SETTERS ---

    public function getId(): ?int { return $this->id; }

    public function getTitlePrivateSpace(): ?string { return $this->titlePrivateSpace; }
    public function setTitlePrivateSpace(string $title): static { $this->titlePrivateSpace = $title; return $this; }

    public function getDescriptionPrivateSpace(): ?string { return $this->descriptionPrivateSpace; }
    public function setDescriptionPrivateSpace(string $description): static { $this->descriptionPrivateSpace = $description; return $this; }

    public function getCapacity(): ?int { return $this->capacity; }
    public function setCapacity(int $capacity): static { $this->capacity = $capacity; return $this; }

    public function getAreaM2(): ?string { return $this->areaM2; }
    public function setAreaM2(string $areaM2): static { $this->areaM2 = $areaM2; return $this; }

    public function getPricePerMonth(): ?string { return $this->pricePerMonth; }
    public function setPricePerMonth(string $price): static { $this->pricePerMonth = $price; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getIsActive(): ?bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getColivingSpace(): ?ColivingSpace { return $this->colivingSpace; }
    public function setColivingSpace(?ColivingSpace $space): static { $this->colivingSpace = $space; return $this; }
}
