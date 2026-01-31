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
    #[Assert\Choice(choices: [
        'Directeur', 'Administratif', 'Enseignant', 'Éducateur', 'Psychologue', 
        'Orthophoniste', 'Kinésithérapeute', 'Chauffeur', 'Ouvrier', 'Agent de sécurité', 'Autre'
    ])]
    private ?string $role = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(choices: ['Administratif', 'Éducatif', 'Médical', 'Technique', 'Services Généraux'])]
    private ?string $service = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $horaireDebut = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $horaireFin = null;

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

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: PersonnelTask::class, orphanRemoval: true)]
    private Collection $tasks;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: PersonnelEvent::class, orphanRemoval: true)]
    private Collection $events;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->pointages = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->events = new ArrayCollection();
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

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getHoraireDebut(): ?\DateTimeInterface
    {
        return $this->horaireDebut;
    }

    public function setHoraireDebut(?\DateTimeInterface $horaireDebut): self
    {
        $this->horaireDebut = $horaireDebut;
        return $this;
    }

    public function getHoraireFin(): ?\DateTimeInterface
    {
        return $this->horaireFin;
    }

    public function setHoraireFin(?\DateTimeInterface $horaireFin): self
    {
        $this->horaireFin = $horaireFin;
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

    /**
     * @return Collection<int, PersonnelTask>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(PersonnelTask $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setPersonnel($this);
        }

        return $this;
    }

    public function removeTask(PersonnelTask $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getPersonnel() === $this) {
                $task->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PersonnelEvent>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(PersonnelEvent $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setPersonnel($this);
        }

        return $this;
    }

    public function removeEvent(PersonnelEvent $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getPersonnel() === $this) {
                $event->setPersonnel(null);
            }
        }

        return $this;
    }
}