<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups as Groups;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[Groups(['std'])]
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[Groups(['std'])]
  #[ORM\Column(length: 180, unique: true)]
  private ?string $email = null;

  #[Groups(['std'])]
  #[ORM\Column(type: "json")]
  private array $roles = ['ROLES_USER'];

  /**
   * @var string The hashed password
   */
  #[Groups(['private'])]
  #[ORM\Column]
  private ?string $password = null;

  #[Groups(['std'])]
  #[ORM\Column]
  private ?bool $is_admin = null;

  #[Groups(['std'])]
  #[ORM\Column(type: Types::DATE_MUTABLE)]
  private ?\DateTimeInterface $start_date = null;

  #[Groups(['std'])]
  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $end_date = null;

  #[Groups('api_token')]
  #[ORM\OneToMany(mappedBy: 'user', targetEntity: ApiToken::class, orphanRemoval: true)]
  private Collection $apiTokens;

  #[Groups(['std'])]
  #[ORM\OneToOne(mappedBy: 'user_id', cascade: ['persist', 'remove'])]
  private ?WorkingHours $workingHours = null;

  public function __construct()
  {
    $this->apiTokens = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
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

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string)$this->email;
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

  public function setRoles(array $roles): static
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

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
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

  /**
   * @return Collection<int, ApiToken>
   */
  public function getApiTokens(): Collection
  {
    return $this->apiTokens;
  }

  public function addApiToken(ApiToken $apiToken): static
  {
    if (!$this->apiTokens->contains($apiToken)) {
      $this->apiTokens->add($apiToken);
      $apiToken->setUser($this);
    }

    return $this;
  }

  public function removeApiToken(ApiToken $apiToken): static
  {
    if ($this->apiTokens->removeElement($apiToken)) {
      // set the owning side to null (unless already changed)
      if ($apiToken->getUser() === $this) {
        $apiToken->setUser(null);
      }
    }

    return $this;
  }

  public function getWorkingHours(): ?WorkingHours
  {
    return $this->workingHours;
  }

  public function setWorkingHours(WorkingHours $workingHours): static
  {
    // set the owning side of the relation if necessary
    if ($workingHours->getUserId() !== $this) {
      $workingHours->setUserId($this);
    }

    $this->workingHours = $workingHours;

    return $this;
  }
}
