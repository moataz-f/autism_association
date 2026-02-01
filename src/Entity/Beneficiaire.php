<?php
// src/Entity/Beneficiaire.php
namespace App\Entity;

use App\Repository\BeneficiaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BeneficiaireRepository::class)]
class Beneficiaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(choices: ['Masculin', 'Féminin'])]
    private ?string $genre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['Léger', 'Modéré', 'Sévère'])]
    private ?string $niveauAutisme = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $diagnostic = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\ManyToMany(targetEntity: Activite::class, inversedBy: 'beneficiaires')]
    private Collection $activites;

    #[ORM\OneToMany(mappedBy: 'beneficiaire', targetEntity: Rapport::class)]
    private Collection $rapports;

    #[ORM\OneToMany(mappedBy: 'beneficiaire', targetEntity: BeneficiaireDocument::class, orphanRemoval: true)]
    private Collection $documents;

    #[ORM\Column(type: 'boolean')]
    private ?bool $actif = true;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'children')]
    #[ORM\JoinTable(name: 'beneficiaire_parents')]
    private Collection $parents;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->rapports = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->dateInscription = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getNiveauAutisme(): ?string
    {
        return $this->niveauAutisme;
    }

    public function setNiveauAutisme(string $niveauAutisme): self
    {
        $this->niveauAutisme = $niveauAutisme;
        return $this;
    }

    public function getDiagnostic(): ?string
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(?string $diagnostic): self
    {
        $this->diagnostic = $diagnostic;
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

    public function getActivites(): Collection
    {
        return $this->activites;
    }

    public function addActivite(Activite $activite): self
    {
        if (!$this->activites->contains($activite)) {
            $this->activites->add($activite);
        }
        return $this;
    }

    public function removeActivite(Activite $activite): self
    {
        $this->activites->removeElement($activite);
        return $this;
    }

    public function getRapports(): Collection
    {
        return $this->rapports;
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

    public function getAge(): int
    {
        return $this->dateNaissance->diff(new \DateTime())->y;
    }

    public function __toString(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function addRapport(Rapport $rapport): static
    {
        if (!$this->rapports->contains($rapport)) {
            $this->rapports->add($rapport);
            $rapport->setBeneficiaire($this);
        }

        return $this;
    }

    public function removeRapport(Rapport $rapport): static
    {
        if ($this->rapports->removeElement($rapport)) {
            // set the owning side to null (unless already changed)
            if ($rapport->getBeneficiaire() === $this) {
                $rapport->setBeneficiaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BeneficiaireDocument>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(User $parent): self
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
        }
        return $this;
    }

    public function removeParent(User $parent): self
    {
        $this->parents->removeElement($parent);
        return $this;
    }
}