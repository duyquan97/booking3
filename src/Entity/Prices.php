<?php

namespace App\Entity;

use App\Repository\PricesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PricesRepository::class)
 * @UniqueEntity(fields={"fromDate","room"}, message="This date is already used")
 * @UniqueEntity(fields={"toDate","room"}, message="This date is already used")
 */
class Prices
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=false)
     *
     */
    private $price;

    /**
     * @ORM\Column(type="date", nullable=false)
     *
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=false)
     *
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    private $toDate;

    /**
     * @ORM\ManyToOne(targetEntity=Rooms::class)
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $room;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(?\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getRoom(): ?Rooms
    {
        return $this->room;
    }

    public function setRoom(?Rooms $room): self
    {
        $this->room = $room;

        return $this;
    }
}
