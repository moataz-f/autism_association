<?php

namespace App\Entity;

use App\Repository\PointageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointageRepository::class)]
class Pointage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pointages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $personnel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureEntree = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureSortie = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $ipAddress = null;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->heureEntree = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getHeureEntree(): ?\DateTimeInterface
    {
        return $this->heureEntree;
    }

    public function setHeureEntree(\DateTimeInterface $heureEntree): self
    {
        $this->heureEntree = $heureEntree;
        return $this;
    }

    public function getHeureSortie(): ?\DateTimeInterface
    {
        return $this->heureSortie;
    }

    public function setHeureSortie(?\DateTimeInterface $heureSortie): self
    {
        $this->heureSortie = $heureSortie;
        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getDuree(): ?\DateInterval
    {
        if ($this->heureEntree && $this->heureSortie) {
            return $this->heureEntree->diff($this->heureSortie);
        }
        return null;
    }
}
