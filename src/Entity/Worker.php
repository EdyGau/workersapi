<?php

namespace App\Entity;

use App\Repository\WorkerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
class Worker implements UserInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("worker:list")]
    private ?int $id = null;

    #[Groups(['worker:list', 'worker:update'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['worker:list', 'worker:update'])]
    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[Groups(['worker:list', 'worker:update'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['worker:list', 'worker:update'])]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['worker:list', 'worker:update'])]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['worker:list', 'worker:update'])]
    private ?string $pesel = null;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'workers')]
    #[ORM\JoinColumn]
    #[Assert\Choice(choices: ["Man", "Woman"], message: "Invalid gender. Only Man/Woman are allowed.")]
    #[Groups(['worker:list', 'worker:update'])]
    private ?Gender $gender = null;

    #[ORM\Column]
    #[Groups(['worker:list', 'worker:update'])]
    /**
     * @ORM\Column(type="json")
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = password_hash($password, PASSWORD_ARGON2I);

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPesel(): ?string
    {
        return $this->pesel;
    }

    public function setPesel(string $pesel): static
    {
        $this->pesel = $pesel;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
