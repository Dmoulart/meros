<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ExpanseRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExpanseRepository::class)
 */
class Expanse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive(
     *     message="Le montant de la dépense doit être supérieur à 0."
     * )
     * @Serializer\Expose
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\Length(
     *      min = 1,
     *      max = 40,
     *      minMessage = "La raison de la dépense ne peut pas compter moins de 1 caractère.",
     *      maxMessage = "La raison de la dépense ne peut pas compter plus de 40 caractères."
     * )
     * @Serializer\Expose
     */
    private $reason;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *      max = 400,
     *      maxMessage = "La raison de la dépense ne peut pas compter plus de 400 caractères."
     * )
     * @Serializer\Expose
     */
    private $details;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose
     */
    private $isSettled;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Serializer\Expose
     */
    private $documents = [];

    /**
     * @ORM\ManyToOne(targetEntity=Vehicle::class, inversedBy="expanses", fetch="EAGER")
     * @Serializer\Expose
     */
    private $vehicle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getIsSettled(): ?bool
    {
        return $this->isSettled;
    }

    public function setIsSettled(bool $isSettled): self
    {
        $this->isSettled = $isSettled;

        return $this;
    }

    public function getDocuments(): ?array
    {
        return $this->documents;
    }

    public function setDocuments(?array $documents): self
    {
        $this->documents = $documents;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
