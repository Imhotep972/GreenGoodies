<?php

namespace App\Entity;

use App\Enum\OrderStatut;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_REFERENCE', fields: ['reference'])]
#[UniqueEntity(fields: ['reference'], message: 'Une commande existe déjà avec cette référence')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Type(\DateTimeImmutable::class)] 
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true,enumType: OrderStatut::class)]
    #[Assert\NotBlank()]
    private ? OrderStatut $status = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotNull()]
    #[Assert\PositiveOrZero()]
    private ?int $amount = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank()]
    private ?string $reference = null;

    #[ORM\Column(nullable: false)]
    private ?bool $archive = false;
    
    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
    * @var Collection<int, OrderLine>
    */
    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: OrderLine::class, cascade: ['persist', 'remove'], orphanRemoval: false)]
    private Collection $orderLines;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = OrderStatut::Pending;
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?OrderStatut
    {
        return  $this->status;
    }

    public function setStatus(OrderStatut $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        if ($amount < 0) 
        {
            throw new \InvalidArgumentException("Amount doit >= 0");   
        }
        $this->amount = $amount;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        if (empty($reference) || $reference === null )
        {
            throw new \InvalidArgumentException("Reference ne peut etre null ou vide");       
        }
        $this->reference = $reference;

        return $this;
    }    

    public function isArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): static
    {
        $this->archive = $archive;

        return $this;
    }   
    
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
    * @return Collection<int, OrderLine>
    */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setOrders($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getOrders() === $this) {
                $orderLine->setOrders(null);
            }
        }

        return $this;
    }


}
