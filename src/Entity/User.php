<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @Serializer\ExclusionPolicy("ALL")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\NotNull(
     *     message="Un email doit être renseigné."
     * )
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas valide."
     * )
     * @Serializer\Expose
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $roles = ["ROLE_USER"];

    /**
     * @var string The hashed password
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 6,
     *      max = 150,
     *      minMessage = "Votre mot de passe doit compter 6 caractères au minimum.",
     *      maxMessage = "Votre mot de passe ne peut pas compter plus de 150 caractères."
     * )
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\Length(
     *      min = 3,
     *      max = 40,
     *      minMessage = "Votre nom doit compter 3 caractères au minimum.",
     *      maxMessage = "Votre nom ne peut pas compter plus de 40 caractères."
     * )
     * @Assert\NotNull(
     *     message="Un utilisateur doit avoir un nom."
     * )
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $names;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Assert\Length(
     *      min = 3,
     *      max = 40,
     *      minMessage = "Votre pseudonyme doit compter 3 caractères au minimum.",
     *      maxMessage = "Votre pseudonyme ne peut pas compter plus de 40 caractères."
     * )
     * @Serializer\Expose
     */
    private $pseudo;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $share;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    private $estimatedMileage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\Expose
     */
    private $currentMileage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNames(): ?string
    {
        return $this->names;
    }

    public function setNames(string $names): self
    {
        $this->names = $names;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getShare(): ?int
    {
        return $this->share;
    }

    public function setShare(int $share): self
    {
        $this->share = $share;

        return $this;
    }

    public function getEstimatedMileage(): ?int
    {
        return $this->estimatedMileage;
    }

    public function setEstimatedMileage(int $estimatedMileage): self
    {
        $this->estimatedMileage = $estimatedMileage;

        return $this;
    }

    public function getCurrentMileage(): ?int
    {
        return $this->currentMileage;
    }

    public function setCurrentMileage(?int $currentMileage): self
    {
        $this->currentMileage = $currentMileage;

        return $this;
    }
}
