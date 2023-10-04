<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(length: 255)]
  private ?string $email = null;

  #[ORM\Column(type: Types::DATE_MUTABLE)]
  private ?\DateTimeInterface $start_date = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $end_date = null;

  #[ORM\Column]
  private ?bool $is_admin = null;

  #[ORM\Column(length: 255)]
  private ?string $password = null;

  #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: WorkingHours::class)]
  private Collection $workingHours;

  public function __construct()
  {
      $this->workingHours = new ArrayCollection();
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

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): static
  {
    $this->email = $email;

    return $this;
  }

  public function getStartDate(): ?\DateTimeInterface
  {
    return $this->start_date;
  }

  public function setStartDate(\DateTimeInterface $start_date): static
  {
    $this->start_date = $start_date;

    return $this;
  }

  public function getEndDate(): ?\DateTimeInterface
  {
    return $this->end_date;
  }

  public function setEndDate(?\DateTimeInterface $end_date): static
  {
    $this->end_date = $end_date;

    return $this;
  }

  public function isIsAdmin(): ?bool
  {
    return $this->is_admin;
  }

  public function setIsAdmin(bool $is_admin): static
  {
    $this->is_admin = $is_admin;

    return $this;
  }

  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @return Collection<int, WorkingHours>
   */
  public function getWorkingHours(): Collection
  {
      return $this->workingHours;
  }

  public function addWorkingHour(WorkingHours $workingHour): static
  {
      if (!$this->workingHours->contains($workingHour)) {
          $this->workingHours->add($workingHour);
          $workingHour->setUserId($this);
      }

      return $this;
  }

  public function removeWorkingHour(WorkingHours $workingHour): static
  {
      if ($this->workingHours->removeElement($workingHour)) {
          // set the owning side to null (unless already changed)
          if ($workingHour->getUserId() === $this) {
              $workingHour->setUserId(null);
          }
      }

      return $this;
  }
}
