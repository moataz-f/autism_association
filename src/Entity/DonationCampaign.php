<?php
namespace App\Entity;

use App\Repository\DonationCampaignRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonationCampaignRepository::class)]
class DonationCampaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantObjectif = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantCollecte = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column]
    private ?bool $actif = true;

    #[ORM\Column]
    private ?bool $principale = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getMontantObjectif(): ?string
    {
        return $this->montantObjectif;
    }

    public function setMontantObjectif(string $montantObjectif): self
    {
        $this->montantObjectif = $montantObjectif;
        return $this;
    }

    public function getMontantCollecte(): ?string
    {
        return $this->montantCollecte;
    }

    public function setMontantCollecte(string $montantCollecte): self
    {
        $this->montantCollecte = $montantCollecte;
        return $this;
    }

    public function getPourcentage(): int
    {
        if ($this->montantObjectif > 0) {
            return (int) (($this->montantCollecte / $this->montantObjectif) * 100);
        }
        return 0;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    public function isPrincipale(): ?bool
    {
        return $this->principale;
    }

    public function setPrincipale(bool $principale): self
    {
        $this->principale = $principale;
        return $this;
    }
}