<?php

namespace App\Entity;

use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: '`products`')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('getProduct')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups('getProduct')]
    private ?string $name = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero()]
    #[Assert\NotNull()]
    #[Groups('getProduct')]
    private ?int $price = null;

    #[ORM\Column(length: 70)]
    #[Assert\NotBlank]
    #[Assert\NotNull()]
    #[Groups('getProduct')]
    private ?string $shortdesc = null;

    #[ORM\Column(length: 1500)]
    #[Assert\NotBlank]
    #[Assert\NotNull()]
    #[Groups('getProduct')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull()]
    #[Groups('getProduct')]
    private ?string $photo = null;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'product')]
    #[Assert\NotNull()]
    private Collection $orderLines;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        if (empty($name))
        {
           throw new \InvalidArgumentException("Le nom du produit ne doit pas être vide.");    
        }
        $this->name = $name;

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
           throw new  \InvalidArgumentException("Le prix du produit doit etre positif ou null.");    
        }

        $this->price = $price;

        return $this;
    }

    public function getShortdesc(): ?string
    {
        return $this->shortdesc;
    }

    public function setShortdesc(string $shortdesc): static
    {
        if (empty($shortdesc))
        {
           throw new \InvalidArgumentException("La description courte ne doit pas etre vide.");    
        }
        $this->shortdesc = $shortdesc;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        if (empty($description))
        {
           throw new  \InvalidArgumentException("La description ne doit pas etre vide.");    
        }        
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        if (empty($photo) || $photo ===null)
        {
           throw new \InvalidArgumentException("Le chemin de la photo ne doit pas etre vide ou null.");    
        }
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setProduct($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProduct() === $this) {
                $orderLine->setProduct(null);
            }
        }

        return $this;
    }

}
