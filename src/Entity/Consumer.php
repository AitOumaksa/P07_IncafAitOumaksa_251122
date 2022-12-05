<?php

namespace App\Entity;

use App\Repository\ConsumerRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

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
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getConsumers"])]
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
