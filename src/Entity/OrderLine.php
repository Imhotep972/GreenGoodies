<?php

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
#[ORM\Table(name: '`orderlines`')]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Positive()]
    #[Assert\NotNull()]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero()]
    #[Assert\NotNull()]
    // prix en centimes, pour etre cohérent avec Stripe
    private ?int $price = null;    

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?Order $orders = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        if ($quantity <= 0)
        {
            throw new \InvalidArgumentException("La quantité doit être strictement positive.");    
        }
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        if ($price < 0)
        {
            throw new \InvalidArgumentException("Le prix ne peut etre négatif.");    
        }
        $this->price = $price;

        return $this;
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
    
        $this->orders = $orders;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
    
        $this->product = $product;

        return $this;
    }

}
