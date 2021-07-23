<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\OneToMany(targetEntity=CartLine::class, mappedBy="cart", orphanRemoval=true)
     */
    private $cartLines;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cartStore")
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="carts")
     */
    private $orderCart;



    public function __construct()
    {
        $this->cartLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|CartLine[]
     */
    public function getCartLines(): Collection
    {
        return $this->cartLines;
    }

    public function addCartLine(CartLine $cartLine): self
    {
        if (!$this->cartLines->contains($cartLine)) {
            $this->cartLines[] = $cartLine;
            $cartLine->setCart($this);
        }

        return $this;
    }

    public function removeCartLine(CartLine $cartLine): self
    {
        if ($this->cartLines->removeElement($cartLine)) {
            // set the owning side to null (unless already changed)
            if ($cartLine->getCart() === $this) {
                $cartLine->setCart(null);
            }
        }

        return $this;
    }

    public function total()
    {
        $total = 0.0;
        /** @var CartLine $cartLine */
        foreach ($this->getCartLines() as $cartLine) {
            $total += $cartLine->total();
        }

        return $total;
    }

    public function getStore(): ?User
    {
        return $this->store;
    }

    public function setStore(?User $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getOrderCart(): ?Order
    {
        return $this->orderCart;
    }

    public function setOrderCart(?Order $orderCart): self
    {
        $this->orderCart = $orderCart;

        return $this;
    }

}
