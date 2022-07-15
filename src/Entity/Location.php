<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Dto\LocationInput;
use App\Dto\LocationOutput;
use App\Repository\LocationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            "denormalization_context" => [
                "groups" => [
                    "location:write",
                    "location:collection:post"
                ]
            ],
            "validation_groups"       => ["Default", "create"]
        ]
    ],
    iri: "locations",
    itemOperations: [
        "get" => [
            "normalization_context" => [
                "groups" => [
                    "location:read",
                    "location:item:get"
                ]
            ]
        ],
        "put" => [
            "validation_groups" => ["Default", "create"]
        ],
    ],
    attributes: ["pagination_items_per_page" => 10],
    denormalizationContext: ["groups" => ["location:write"]],
    input: LocationInput::CLASS,
    normalizationContext: ["groups" => ["location:read"]],
    output: LocationOutput::CLASS
)]
#[ApiFilter(PropertyFilter::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 36)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Serializer\Groups(["location:read", "location:write", "shipment:item:get", "shipment:write"])]
    private $postcode;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Serializer\Groups(["location:read", "location:write", "shipment:item:get", "shipment:write"])]
    private $city;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Serializer\Groups(["location:read", "location:write", "shipment:item:get", "shipment:write"])]
    private $country;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Shipment::class, mappedBy: 'route', cascade: ["persist"], orphanRemoval: true)]
    #[Serializer\Groups(["location:collection:post"])]
    private $shipments;

    #[ORM\Column(type: 'uuid')]
    private $uuid;

    public function __construct(Uuid $uuid = null)
    {
        $this->shipments = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->uuid      = $uuid ?? Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Shipment>
     */
    public function getShipments(): Collection
    {
        return $this->shipments;
    }

    public function addShipment(Shipment $shipment): self
    {
        if (!$this->shipments->contains($shipment)) {
            $this->shipments[] = $shipment;
            $shipment->addRoute($this);
        }

        return $this;
    }

    public function removeShipment(Shipment $shipment): self
    {
        if ($this->shipments->removeElement($shipment)) {
            $shipment->removeRoute($this);
        }

        return $this;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
