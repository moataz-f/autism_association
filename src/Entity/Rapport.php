<?php
// src/Entity/Rapport.php
namespace App\Entity;

use App\Repository\RapportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RapportRepository::class)]
class Rapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rapports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Beneficiaire $beneficiaire = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $contenu = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['Évaluation', 'Suivi', 'Médical', 'Comportemental', 'Autre'])]
    private ?string $type = null;

    #[ORM\ManyToOne]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeneficiaire(): ?Beneficiaire
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?Beneficiaire $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;
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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}