<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $token = null;

  #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'apiTokens')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Users $user = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getToken(): ?string
  {
    return $this->token;
  }

  public function setToken(string $token): static
  {
    $this->token = $token;

    return $this;
  }

  public function getUser(): ?Users
  {
    return $this->user;
  }

  public function setUser(?Users $user): static
  {
    $this->user = $user;

    return $this;
  }
}
