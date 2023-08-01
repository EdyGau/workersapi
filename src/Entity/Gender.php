<?php

namespace App\Entity;

use App\Repository\GenderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraint;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: GenderRepository::class)]
class Gender extends Constraint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @ORM\Column(type="json")
     * @Groups({"worker:list", "worker:update"})
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'gender', targetEntity: Worker::class)]
    private Collection $workers;

    /**
     * @ORM\Column(type="json")
     * @Groups({"worker:list", "worker:update"})
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public $payload;

    public function __construct()
    {
        $this->workers = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Worker>
     */
    public function getWorkers(): Collection
    {
        return $this->workers;
    }

    public function addWorker(Worker $worker): static
    {
        if (!$this->workers->contains($worker)) {
            $this->workers->add($worker);
            $worker->setGender($this);
        }

        return $this;
    }

    public function removeWorker(Worker $worker): static
    {
        if ($this->workers->removeElement($worker)) {
            if ($worker->getGender() === $this) {
                $worker->setGender(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
