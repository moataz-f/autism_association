<?php
// src/Entity/Famille.php
namespace App\Entity;

use App\Repository\FamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FamilleRepository::class)]
class Famille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom du responsable est obligatoire')]
    private ?string $nomResponsable = null;

    #[ORM\Column(length: 100)]
    private ?string $prenomResponsable = null;

    #[ORM\Column(length: 20)]
    #[Assert\Regex(pattern: '/^[0-9]{8}$/', message: 'Numéro de téléphone invalide')]
    private ?string $telephone = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email(message: 'Email invalide')]
    private ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresse = null;

    #[ORM\OneToMany(mappedBy: 'famille', targetEntity: Beneficiaire::class)]
    private Collection $beneficiaires;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateInscription = null;

    public function __construct()
    {
        $this->beneficiaires = new ArrayCollection();
        $this->dateInscription = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomResponsable(): ?string
    {
        return $this->nomResponsable;
    }

    public function setNomResponsable(string $nomResponsable): self
    {
        $this->nomResponsable = $nomResponsable;
        return $this;
    }

    public function getPrenomResponsable(): ?string
    {
        return $this->prenomResponsable;
    }

    public function setPrenomResponsable(string $prenomResponsable): self
    {
        $this->prenomResponsable = $prenomResponsable;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
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
            $beneficiaire->setFamille($this);
        }
        return $this;
    }

    public function removeBeneficiaire(Beneficiaire $beneficiaire): self
    {
        if ($this->beneficiaires->removeElement($beneficiaire)) {
            if ($beneficiaire->getFamille() === $this) {
                $beneficiaire->setFamille(null);
            }
        }
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nomResponsable . ' ' . $this->prenomResponsable;
    }
}