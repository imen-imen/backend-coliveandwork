<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ColivingSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Get(security: "is_granted('PUBLIC_ACCESS')"),
    ],
    normalizationContext: ['groups' => ['coliving:read']],
)]
#[ORM\Entity(repositoryClass: ColivingSpaceRepository::class)]
#[ORM\Table(name: 'coliving_space')]
class ColivingSpace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['coliving:read', 'private:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['coliving:read', 'private:read'])]
    private ?string $titleColivingSpace = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['coliving:read'])]
    private ?string $descriptionColivingSpace = null;

    #[ORM\Column(length: 50)]
    #[Groups(['coliving:read'])]
    private ?string $housingType = null;

    #[ORM\Column]
    #[Groups(['coliving:read'])]
    private ?int $roomCount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Groups(['coliving:read'])]
    private ?string $totalAreaM2 = null;

    #[ORM\Column]
    #[Groups(['coliving:read'])]
    private ?int $capacityMax = null;

    #[ORM\Column]
    #[Groups(['coliving:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['coliving:read'])]
    private ?bool $isActive = true;

    #[ORM\OneToMany(targetEntity: PrivateSpace::class, mappedBy: 'colivingSpace')]
    private Collection $privateSpaces;

    #[ORM\ManyToOne(inversedBy: 'colivingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\ManyToOne(inversedBy: 'colivingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'colivingSpace', orphanRemoval: true)]
    private Collection $photos;

    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'colivingSpace', orphanRemoval: true)]
    private Collection $verificationSpaces;

    #[ORM\ManyToMany(targetEntity: Amenity::class, mappedBy: 'colivingSpaces')]
    private Collection $amenities;

    #[ORM\ManyToOne(targetEntity: ColivingCity::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['coliving:read'])]
    private ?ColivingCity $colivingCity = null;

    public function __construct()
    {
        $this->privateSpaces = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->amenities = new ArrayCollection();
    }

    /* ─────────────────────────────────────────────
     * GETTERS & SETTERS COMPLETS
     * ───────────────────────────────────────────── */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleColivingSpace(): ?string
    {
        return $this->titleColivingSpace;
    }

    public function setTitleColivingSpace(string $title): static
    {
        $this->titleColivingSpace = $title;
        return $this;
    }

    public function getDescriptionColivingSpace(): ?string
    {
        return $this->descriptionColivingSpace;
    }

    public function setDescriptionColivingSpace(string $description): static
    {
        $this->descriptionColivingSpace = $description;
        return $this;
    }

    public function getHousingType(): ?string
    {
        return $this->housingType;
    }

    public function setHousingType(string $type): static
    {
        $this->housingType = $type;
        return $this;
    }

    public function getRoomCount(): ?int
    {
        return $this->roomCount;
    }

    public function setRoomCount(int $count): static
    {
        $this->roomCount = $count;
        return $this;
    }

    public function getTotalAreaM2(): ?string
    {
        return $this->totalAreaM2;
    }

    public function setTotalAreaM2(string $area): static
    {
        $this->totalAreaM2 = $area;
        return $this;
    }

    public function getCapacityMax(): ?int
    {
        return $this->capacityMax;
    }

    public function setCapacityMax(int $capacity): static
    {
        $this->capacityMax = $capacity;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getPrivateSpaces(): Collection
    {
        return $this->privateSpaces;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function getVerificationSpaces(): Collection
    {
        return $this->verificationSpaces;
    }

    public function getAmenities(): Collection
    {
        return $this->amenities;
    }

    public function addAmenity(Amenity $amenity): static
    {
        if (!$this->amenities->contains($amenity)) {
            $this->amenities->add($amenity);
            $amenity->addColivingSpace($this);
        }
        return $this;
    }

    public function removeAmenity(Amenity $amenity): static
    {
        if ($this->amenities->removeElement($amenity)) {
            $amenity->removeColivingSpace($this);
        }
        return $this;
    }

    public function getColivingCity(): ?ColivingCity
    {
        return $this->colivingCity;
    }

    public function setColivingCity(?ColivingCity $colivingCity): static
    {
        $this->colivingCity = $colivingCity;
        return $this;
    }
}
