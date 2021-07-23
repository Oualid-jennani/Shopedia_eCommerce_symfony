<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    const STATUS_INITIATED = 'INITIATED';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_VALIDATED = 'VALIDATED';
    const STATUS_IN_TRANSIT = 'IN TRANSIT';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_RETURNED = 'RETURNED';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trackNumber;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="datetime")
     */
    private $orderDate;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $orderBookDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $bookTable;

    /**
     * @ORM\Column(type="text", length=255,nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=DeliveryOption::class, inversedBy="orders")
     */
    private $deliveryOption;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="orders")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orderCustomer")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orderDriver")
     */
    private $driver;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="orderCart")
     */
    private $carts;

    /**
     * @ORM\OneToMany(targetEntity=StatusHistory::class, mappedBy="statusOrder")
     */
    private $statusHistory;



    public function __construct()
    {
        $this->carts = new ArrayCollection();
        $this->statusHistory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }

    /**
     * @param mixed $trackNumber
     */
    public function setTrackNumber($trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderBookDate(): ?\DateTimeInterface
    {
        return $this->orderBookDate;
    }

    public function setOrderBookDate(\DateTimeInterface $orderBookDate): self
    {
        $this->orderBookDate = $orderBookDate;

        return $this;
    }

    public function getBookTable(): ?bool
    {
        return $this->bookTable;
    }

    public function setBookTable(?bool $bookTable): self
    {
        $this->bookTable = $bookTable;

        return $this;
    }

    public function getDeliveryOption(): ?DeliveryOption
    {
        return $this->deliveryOption;
    }

    public function setDeliveryOption(?DeliveryOption $deliveryOption): self
    {
        $this->deliveryOption = $deliveryOption;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCustomer(): ?user
    {
        return $this->customer;
    }

    public function setCustomer(?user $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getDriver(): ?user
    {
        return $this->driver;
    }

    public function setDriver(?user $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setOrderCart($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getOrderCart() === $this) {
                $cart->setOrderCart(null);
            }
        }

        return $this;
    }

    public function total()
    {
        $total = 0.0;
        /** @var Cart $cart */
        foreach ($this->getCarts() as $cart) {
            $total += $cart->total();
        }

        return $total;
    }

    /**
     * @return Collection|StatusHistory[]
     */
    public function getStatusHistory(): Collection
    {
        return $this->statusHistory;
    }

    public function addStatusHistory(StatusHistory $statusHistory): self
    {
        if (!$this->statusHistory->contains($statusHistory)) {
            $this->statusHistory[] = $statusHistory;
            $statusHistory->setStatusOrder($this);
        }

        return $this;
    }

    public function removeStatusHistory(StatusHistory $statusHistory): self
    {
        if ($this->statusHistory->removeElement($statusHistory)) {
            // set the owning side to null (unless already changed)
            if ($statusHistory->getStatusOrder() === $this) {
                $statusHistory->setStatusOrder(null);
            }
        }

        return $this;
    }

}
