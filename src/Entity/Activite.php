<?php
// src/Entity/Activite.php
namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['Éducative', 'Thérapeutique', 'Sociale', 'Sportive', 'Artistique'])]
    private ?string $type = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lieu = null;

    #[ORM\ManyToMany(targetEntity: Beneficiaire::class, mappedBy: 'activites')]
    private Collection $beneficiaires;

    #[ORM\ManyToOne(inversedBy: 'activites')]
    private ?Personnel $personnel = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $capaciteMax = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $actif = true;

    public function __construct()
    {
        $this->beneficiaires = new ArrayCollection();
    }

    // Getters et Setters
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getBeneficiaires(): Collection
    {
        return $this->beneficiaires;
    }

    public function addBeneficiaire(Beneficiaire $beneficiaire): self
    {
        if (!$this->beneficiaires->contains($beneficiaire)) {
            $this->beneficiaires->add($beneficiaire);
            $beneficiaire->addActivite($this);
        }
        return $this;
    }

    public function removeBeneficiaire(Beneficiaire $beneficiaire): self
    {
        if ($this->beneficiaires->removeElement($beneficiaire)) {
            $beneficiaire->removeActivite($this);
        }
        return $this;
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

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(?int $capaciteMax): self
    {
        $this->capaciteMax = $capaciteMax;
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

    public function getNombreParticipants(): int
    {
        return $this->beneficiaires->count();
    }

    public function __toString(): string
    {
        return $this->titre;
    }
}
