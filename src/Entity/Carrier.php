<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\CarrierInput;
use App\Dto\CarrierOutput;
use App\Repository\CarrierRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarrierRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            "denormalization_context" => [
                "groups" => [
                    "carrier:write",
                    "carrier:collection:post"
                ]
            ],
            "validation_groups"       => ["Default", "create"]
        ]
    ],
    iri: "carriers",
    itemOperations: [
        "get" => [
            "normalization_context" => [
                "groups" => [
                    "carrier:read",
                    "carrier:item:get"
                ]
            ]
        ],
        "put" => [
            "validation_groups" => ["Default", "create"]
        ],
    ],
    attributes: ["pagination_items_per_page" => 10],
    denormalizationContext: ["groups" => ["carrier:write"]],
    input: CarrierInput::CLASS,
    normalizationContext: ["groups" => ["carrier:read"]],
    output: CarrierOutput::class
)]
class Carrier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 150)]
    #[Assert\NotBlank(["groups" => ['create']])]
    #[Serializer\Groups(["shipment:write", "carrier:read", "carrier:write", "shipment:item:get"])]
    private $name;

    #[ORM\Column(type: 'string', length: 150)]
    #[Assert\NotBlank(["groups" => ['create']])]
    #[Assert\Email(["groups" => ['create']])]
    #[Serializer\Groups(["shipment:write", "carrier:read", "carrier:write", "shipment:item:get"])]
    private $email;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'carrier', targetEntity: Shipment::class, cascade: ["persist"], orphanRemoval: true)]
    #[Serializer\Groups(["carrier:write"])]
    #[ApiProperty(readableLink: true)]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
            $shipment->setCarrier($this);
        }

        return $this;
    }

    public function removeShipment(Shipment $shipment): self
    {
        if ($this->shipments->removeElement($shipment)) {
            // set the owning side to null (unless already changed)
            if ($shipment->getCarrier() === $this) {
                $shipment->setCarrier(null);
            }
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
