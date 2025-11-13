<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AmenityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AmenityRepository::class)]
#[ORM\Table(name: 'amenity')]
#[ApiResource(
    normalizationContext: ['groups' => ['amenity:read']],
    denormalizationContext: ['groups' => ['amenity:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'ipartial',
    'amenityType' => 'exact'
])]
class Amenity
{
    #[Groups(['amenity:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['amenity:read', 'amenity:write'])]
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[Groups(['amenity:read', 'amenity:write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Groups(['amenity:read', 'amenity:write'])]
    #[ORM\Column(length: 50)]
    private ?string $amenityType = null;

    #[ORM\ManyToMany(targetEntity: ColivingSpace::class, mappedBy: 'amenities')]
    private Collection $colivingSpaces;

    #[ORM\ManyToMany(targetEntity: PrivateSpace::class, mappedBy: 'amenities')]
    private Collection $privateSpaces;

    public function __construct()
    {
        $this->colivingSpaces = new ArrayCollection();
        $this->privateSpaces = new ArrayCollection();
    }

    // --- getters/setters ---
    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getAmenityType(): ?string { return $this->amenityType; }
    public function setAmenityType(string $amenityType): static { $this->amenityType = $amenityType; return $this; }
}
