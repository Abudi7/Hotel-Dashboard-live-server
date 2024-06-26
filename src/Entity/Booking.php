<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startdate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $enddate = null;

    #[ORM\Column(length: 255)]
    private ?string $customername = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?Rooms $Rooms = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedat = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $Address = null;

    
    /**
     * @ORM\Column(type="float")
     */
    private $pricePerNight;

    #[ORM\Column(length: 255)]
    private ?string $invoicenumber = null;

    #[ORM\Column]
    private ?bool $switcher = null;

    public function __construct()
    {
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): static
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEnddate(): ?\DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(\DateTimeInterface $enddate): static
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getCustomername(): ?string
    {
        return $this->customername;
    }

    public function setCustomername(string $customername): static
    {
        $this->customername = $customername;

        return $this;
    }

    public function getRooms(): ?Rooms
    {
        return $this->Rooms;
    }

    public function setRooms(?Rooms $Rooms): static
    {
        $this->Rooms = $Rooms;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeInterface $createdat): static
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function getUpdatedat(): ?\DateTimeInterface
    {
        return $this->updatedat;
    }

    public function setUpdatedat(?\DateTimeInterface $updatedat): static
    {
        $this->updatedat = $updatedat;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->Address;
    }

    public function setAddress(?Address $Address): static
    {
        $this->Address = $Address;

        return $this;
    }

     /**
     * Calculate the number of nights for the booking.
     *
     * @return int|null The number of nights or null if the start or end date is not set.
     */
    public function getNumberOfNights(): ?int
    {
        if ($this->startdate === null || $this->enddate === null) {
            return null;
        }

        $interval = $this->startdate->diff($this->enddate);

        return $interval->days;
    }

    public function getInvoicenumber(): ?string
    {
        return $this->invoicenumber;
    }

    public function setInvoicenumber(string $invoicenumber): static
    {
        $this->invoicenumber = $invoicenumber;

        return $this;
    }

    public function isSwitcher(): ?bool
    {
        return $this->switcher;
    }

    public function setSwitcher(bool $switcher): static
    {
        $this->switcher = $switcher;

        return $this;
    }
    


}
