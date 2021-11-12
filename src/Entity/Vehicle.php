<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=VehicleRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 */
class Vehicle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("vehicle")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @Assert\NotNull(
     *     message="Une voiture doit avoir un nom."
     * )
     * @ORM\Column(type="string", length=40, unique=true)
     * @Serializer\Expose
     */
    private $name;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 30,
     *      minMessage = "Le nom de la marque de la voiture ne peut pas compter moins de 3 caractères.",
     *      maxMessage = "Le nom de la marque de la voiture ne peut pas compter plus de 30 caractères."
     * )
     * @ORM\Column(type="string", length=30)
     * @Serializer\Expose
     */
    private $brand;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 1,
     *      max = 40,
     *      minMessage = "Le nom de modèle de la voiture ne peut pas compter moins de 1 caractère.",
     *      maxMessage = "Le nom de modèle de la voiture ne peut pas compter plus de 40 caractères."
     * )
     * @ORM\Column(type="string", length=60)
     * @Serializer\Expose
     */
    private $model;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=6)
     * @Assert\Regex(
     *     "/([a-f0-9]{3}){1,2}\b/i",
     *      message="La couleur doit être au format hexadécimal."
     * )
     * @Serializer\Expose
     */
    private $color;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="Le kilométrage ne peut être inférieur à 0."
     * )
     * @Serializer\Expose
     */
    private $mileage;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(
     *     message="Le nombre de places ne peut être inférieur à 0."
     * )
     * @Serializer\Expose
     */
    private $seats;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=30)
     * @Assert\Choice(
     *     {"gasoline", "diesel", "electricity"},
     *     message="Le type de carburant n'est pas reconnu."
     * )
     * @Serializer\Expose
     */
    private $fuelType;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=30)
     * @Serializer\Expose
     */
    private $city;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=80)
     * @Serializer\Expose
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Positive(
     *     message="Le numéro de rue ne peut être inférieur à 0."
     * )
     * @Serializer\Expose
     */
    private $streetNumber;

    /**
     * todo : Calling getBookings may cause an infinite loop (vehicles is eager loaded in booking)... We need to either remove this prop or fix the problem
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="vehicle",cascade={"remove"})
     * @Ignore
     * @Serializer\Expose
     */
    private $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): self
    {
        $this->seats = $seats;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(string $fuelType): self
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setVehicle($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getVehicle() === $this) {
                $booking->setVehicle(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName()." ".$this->getBrand()." ".$this->getModel();
    }
}
