<?php

namespace App\Entity;

use App\Repository\ConsumerRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 *  @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "consumer.details",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getConsumers")
 * )
 * 
 *  @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route(
 *         "consumer.create",
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         groups = { "getConsumers" },
 *         excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *     )
 * )
 *  @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *         "consumer.delete",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         groups = { "getConsumers" },
 *         excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *     )
 * )
 *
 */

#[ORM\Entity(repositoryClass: ConsumerRepository::class)]
class Consumer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getConsumers"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    #[Assert\NotBlank(message: "Vous devez entrez un nom d'utilisateur")]
    #[Assert\Length(min: 5, minMessage: "Veuillez avoir au moins 5 caractères", max: 50, maxMessage: "Le nom ne doit pas faire plus de 30 caractères")]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    #[Assert\NotBlank(message: "Vous devez entrez un E-mail")]
    #[Assert\Regex(
        pattern : '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/',
        message : "Veuillez renseigner un email valide. Exemple : 'exemple@exemple.exemple'"
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    #[Assert\NotBlank(message: "Vous devez entrez la ville")]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    #[Assert\NotBlank(message: "Vous devez entrez le pays")]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    #[Assert\NotBlank(message: "Vous devez entrez un numéro de télephone")]
    private ?string $phoneNumber = null;

    #[ORM\ManyToOne(inversedBy: 'consumer')]
    #[Groups(["getConsumers" , "getClients"])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
