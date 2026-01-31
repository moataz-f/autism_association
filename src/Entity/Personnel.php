<?php
// src/Entity/Personnel.php
namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Personnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $qrToken = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['Éducateur', 'Psychologue', 'Orthophoniste', 'Administrateur', 'Autre'])]
    private ?string $role = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateEmbauche = null;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Activite::class)]
    private Collection $activites;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Pointage::class, orphanRemoval: true)]
    private Collection $pointages;

    #[ORM\Column(type: 'boolean')]
    private ?bool $actif = true;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $specialisation = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->pointages = new ArrayCollection();
        $this->dateEmbauche = new \DateTime();
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
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

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): self
    {
        $this->dateEmbauche = $dateEmbauche;
        return $this;
    }

    public function getActivites(): Collection
    {
        return $this->activites;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getSpecialisation(): ?string
    {
        return $this->specialisation;
    }

    public function setSpecialisation(?string $specialisation): self
    {
        $this->specialisation = $specialisation;
        return $this;
    }

    public function getQrToken(): ?string
    {
        return $this->qrToken;
    }

    public function setQrToken(?string $qrToken): self
    {
        $this->qrToken = $qrToken;
        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (null === $this->qrToken) {
            $this->qrToken = bin2hex(random_bytes(16));
        }
    }

    public function __toString(): string
    {
        return $this->nom . ' ' . $this->prenom . ' (' . $this->role . ')';
    }

    public function addActivite(Activite $activite): static
    {
        if (!$this->activites->contains($activite)) {
            $this->activites->add($activite);
            $activite->setPersonnel($this);
        }

        return $this;
    }

    public function removeActivite(Activite $activite): static
    {
        if ($this->activites->removeElement($activite)) {
            // set the owning side to null (unless already changed)
            if ($activite->getPersonnel() === $this) {
                $activite->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pointage>
     */
    public function getPointages(): Collection
    {
        return $this->pointages;
    }

    public function addPointage(Pointage $pointage): self
    {
        if (!$this->pointages->contains($pointage)) {
            $this->pointages->add($pointage);
            $pointage->setPersonnel($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getPersonnel() === $this) {
                $pointage->setPersonnel(null);
            }
        }

        return $this;
    }
}