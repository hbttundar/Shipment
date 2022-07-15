<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Dto\ShipmentInput;
use App\Dto\ShipmentOutput;
use App\Repository\ShipmentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
#[ORM\EntityListeners(["App\Doctrine\ShipmentPrePersistListener"])]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            "denormalization_context" => [
                "groups" => [
                    "shipment:write",
                    "shipment:collection:post"
                ]
            ],
            "validation_groups"       => ["Default", "create"]
        ]
    ],
    iri: "shipments",
    itemOperations: [
        "get" => [
            "normalization_context" => [
                "groups" => [
                    "shipment:read",
                    "shipment:item:get"
                ]
            ]
        ],
        "put" => [
            "validation_groups" => ["Default", "create"]
        ],
    ],
    attributes: [
        "pagination_items_per_page" => 10,
    ],
    denormalizationContext: ["groups" => ["shipment:write"]],
    input: ShipmentInput::CLASS,
    normalizationContext: ["groups" => ["shipment:read"]],
    output: ShipmentOutput::CLASS
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(SearchFilter::class, properties: [
    "company"       => "exact",
    "company.name"  => "partial",
    "company.email" => "partial",
])]
#[ApiFilter(RangeFilter::class, properties: ["price", "distance"])]
class Shipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'uuid')]
    private Uuid $uuid;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Serializer\Groups(["shipment:write", "company:write", "carrier:write"])]
    private int $distance;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Serializer\Groups(["shipment:write", "company:write", "carrier:write"])]
    private int $time;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Serializer\ignore]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Company::class, cascade: ["persist"], inversedBy: 'shipments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotNull]
    #[Serializer\Groups(["shipment:write", "location:write", "location:write"])]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: Carrier::class, cascade: ["persist"], inversedBy: 'shipments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotNull]
    #[Serializer\Groups(["shipment:write", "location:write", "location:write"])]
    private Carrier $carrier;

    #[ORM\ManyToMany(targetEntity: Location::class, inversedBy: 'shipments', cascade: ["persist"])]
    #[ORM\JoinTable(name: 'shipment_location')]
    #[ORM\JoinColumn(name: "location_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "shipment_id", referencedColumnName: "id")]
    #[Assert\Valid]
    #[Assert\NotNull]
    #[Serializer\Groups(["shipment:write", "location:write", "location:write"])]
    private Collection $route;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $updatedAt;


    public function __construct(Uuid $uuid = null)
    {
        $this->route     = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->uuid      = $uuid ?? Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    public function setCarrier(?Carrier $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getRoute(): Collection
    {
        return $this->route;
    }

    public function addRoute(Location $route): self
    {
        if (!$this->route->contains($route)) {
            $this->route[] = $route;
        }

        return $this;
    }

    public function removeRoute(Location $route): self
    {
        $this->route->removeElement($route);

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

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
}
