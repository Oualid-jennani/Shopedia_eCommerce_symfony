<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatusRepository::class)
 */
class StatusHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $statusDate;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="statusHistory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statusOrder;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatusDate(): ?\DateTimeInterface
    {
        return $this->statusDate;
    }

    public function setStatusDate(\DateTimeInterface $statusDate): self
    {
        $this->statusDate = $statusDate;

        return $this;
    }

    public function getStatusOrder(): ?Order
    {
        return $this->statusOrder;
    }

    public function setStatusOrder(?Order $statusOrder): self
    {
        $this->statusOrder = $statusOrder;

        return $this;
    }
}
