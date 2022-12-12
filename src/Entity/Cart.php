<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cart', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartsProducts::class)]
    private Collection $cartsProducts;

    public function __construct()
    {
        $this->cartsProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, CartsProducts>
     */
    public function getCartsProducts(): Collection
    {
        return $this->cartsProducts;
    }

    public function addCartsProduct(CartsProducts $cartsProduct): self
    {
        if (!$this->cartsProducts->contains($cartsProduct)) {
            $this->cartsProducts->add($cartsProduct);
            $cartsProduct->setCart($this);
        }

        return $this;
    }

    public function removeCartsProduct(CartsProducts $cartsProduct): self
    {
        if ($this->cartsProducts->removeElement($cartsProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartsProduct->getCart() === $this) {
                $cartsProduct->setCart(null);
            }
        }

        return $this;
    }
}
