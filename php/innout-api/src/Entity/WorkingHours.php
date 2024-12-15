<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use App\Repository\WorkingHoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkingHoursRepository::class)]
class WorkingHours
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: Types::DATE_MUTABLE)]
  private ?\DateTimeInterface $work_date = null;

  #[ORM\Column(nullable: true)]
  private ?int $worked_time = null;

  #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $time1 = null;

  #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $time2 = null;

  #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $time3 = null;

  #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $time4 = null;

  #[ORM\ManyToOne(inversedBy: 'workingHours')]
  private ?Users $user_id = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getWorkDate(): ?\DateTimeInterface
  {
    return $this->work_date;
  }

  public function setWorkDate(\DateTimeInterface $work_date): static
  {
    $this->work_date = $work_date;

    return $this;
  }

  public function getWorkedTime(): ?int
  {
    return $this->worked_time;
  }

  public function setWorkedTime(?int $worked_time): static
  {
    $this->worked_time = $worked_time;

    return $this;
  }

  public function getUserId(): ?Users
  {
    return $this->user_id;
  }

  public function setUserId(string $user_id, EntityManager $entityManager): static
  {
    $user = $entityManager->getRepository(Users::class)->find($user_id);
    $this->user_id = $user;

    return $this;
  }

  public function getNextTime(): string|null
  {
    if (!$this->getTime1()) return 'time1';
    if (!$this->getTime2()) return 'time2';
    if (!$this->getTime3()) return 'time3';
    if (!$this->getTime4()) return 'time4';

    return null;
  }

  public function getTime1(): ?\DateTimeInterface
  {
    return $this->time1;
  }

  public function setTime1(?\DateTimeInterface $time1): static
  {
    $this->time1 = $time1;

    return $this;
  }

  public function getTime2(): ?\DateTimeInterface
  {
    return $this->time2;
  }

  public function setTime2(?\DateTimeInterface $time2): static
  {
    $this->time2 = $time2;

    return $this;
  }

  public function getTime3(): ?\DateTimeInterface
  {
    return $this->time3;
  }

  public function setTime3(?\DateTimeInterface $time3): static
  {
    $this->time3 = $time3;

    return $this;
  }

  public function getTime4(): ?\DateTimeInterface
  {
    return $this->time4;
  }

  public function setTime4(?\DateTimeInterface $time4): static
  {
    $this->time4 = $time4;

    return $this;
  }
}
