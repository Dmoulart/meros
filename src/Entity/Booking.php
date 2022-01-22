<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Accessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Booking model.
 * TODO: Handle the depth of the serialization better. Relations must only be returned as id. The current method to do this
 * is quite hugly, find a more appropriate one.
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      max = 400,
     *      maxMessage = "Les informations de la réservations ne peuvent excéder 400 caractères."
     * )
     * @Serializer\Expose
     */
    private $informations;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    private $isOpen;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(
     *     message="La date de départ doit respecter le format 'Année-Mois-Jour Heure:Minutes:Secondes'."
     * )
     * @Serializer\Expose
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(
     *     message="La date d'arrivée doit respecter le format 'Année-Mois-Jour Heure:Minutes:Secondes'."
     * )
     * @Serializer\Expose
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero(
     *     message="Le kilométrage de début de réservation ne peut être inférieur à 0."
     * )
     * @Serializer\Expose
     */
    private $startMileage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero(
     *     message="Le kilométrage de fin de réservation ne peut être inférieur à 0."
     * )
     * @Serializer\Expose
     */
    private $endMileage;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    private $isCompleted;

    /**
     * @ORM\ManyToOne(targetEntity=Vehicle::class, inversedBy="bookings")
     * @Serializer\Expose
     * @Accessor(getter="getVehicleId")
     * @Ignore
     */
    private $vehicle;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="bookings")
     * @Serializer\Expose
     * @Accessor(getter="getUsersId")
     * @Ignore
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    public function getIsOpen(): ?bool
    {
        return $this->isOpen;
    }

    public function setIsOpen(bool $isOpen): self
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface | string $startDate): self
    {
        $date = $startDate;

        if(is_string($startDate)){
            $date = date_create_from_format('Y-m-d H:i:s', $startDate);
            if(!$date) $date = null;
        }

        $this->startDate = $date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface | string $endDate): self
    {
        $date = $endDate;

        if(is_string($endDate)){
            $date = date_create_from_format('Y-m-d H:i:s', $endDate);
            if(!$date) $date = null;
        }

        $this->endDate = $date;

        return $this;
    }

    public function getStartMileage(): ?int
    {
        return $this->startMileage;
    }

    public function setStartMileage(?int $startMileage): self
    {
        $this->startMileage = $startMileage;

        return $this;
    }

    public function getEndMileage(): ?int
    {
        return $this->endMileage;
    }

    public function setEndMileage(?int $endMileage): self
    {
        $this->endMileage = $endMileage;

        return $this;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle | int $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getVehicleId(): ?int
    {
        return $this->vehicle->getId() ?? null;
    }
    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return array
     */
    public function getUsersId(): array
    {
        return $this->users->map(function($user){
            return $user->getId();
        })->toArray();
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
}
