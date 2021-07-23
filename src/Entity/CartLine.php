<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CartLineRepository::class)
 */
class CartLine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull(message="Please provide a quantity")
     * @Assert\NotBlank(message="This value should not be blank")
     * @Assert\Positive(message="Quantity has an invalid value")
     * @Assert\Regex(pattern="/^(\d+)$/", message="The quantity should be a valid numeric")
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Variant::class, inversedBy="cartLines",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $variant;

    /**
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="cartLines",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cart;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    public function setVariant(?Variant $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }



    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function total()
    {
        return $this->getVariant()->getPrice() * $this->getQuantity();
    }

}