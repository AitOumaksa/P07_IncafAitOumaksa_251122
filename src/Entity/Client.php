<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Adresse E-mail existe déja')]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getConsumers" , "getClients"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["getClients"])]
    #[Assert\NotBlank(message: "Vous devez entrez un E-mail")]
    #[Assert\Regex(
        pattern : '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/',
        message : "Veuillez renseigner un email valide. Exemple : 'exemple@exemple.exemple'"
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(["getClients"])]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[Assert\Regex(
        pattern : '/^((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})+$/',
        message : "Mot de passe doit contenir 8 caractères dont au minimum une majuscule, une minuscule, un caractère numérique et un caractère spécial"
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers" , "getClients"])]
    #[Assert\NotBlank(message: "Veuillez renseigner ce champ")]
    #[Assert\Length(min: 3, minMessage: "Veuillez avoir au moins 4 caractères", max: 50, maxMessage: "Le nom ne doit pas faire plus de 50 caractères")]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Consumer::class)]
    #[Groups(["getClients"])]
    private Collection $consumer;

    public function __construct()
    {
        $this->consumer = new ArrayCollection();
    }

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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /**
     * @return Collection<int, Consumer>
     */
    public function getConsumer(): Collection
    {
        return $this->consumer;
    }

    public function addConsumer(Consumer $consumer): self
    {
        if (!$this->consumer->contains($consumer)) {
            $this->consumer->add($consumer);
            $consumer->setClient($this);
        }

        return $this;
    }

    public function removeConsumer(Consumer $consumer): self
    {
        if ($this->consumer->removeElement($consumer)) {
            // set the owning side to null (unless already changed)
            if ($consumer->getClient() === $this) {
                $consumer->setClient(null);
            }
        }

        return $this;
    }
}
