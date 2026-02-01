<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numtlf = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Personnel::class)]
    private ?Personnel $personnel = null;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $isApproved = false;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $registrationRole = null; // PARENT, DONOR, VOLUNTEER

    #[ORM\ManyToMany(targetEntity: Beneficiaire::class, mappedBy: 'parents')]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
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

    public function getNumtlf(): ?string
    {
        return $this->numtlf;
    }

    public function setNumtlf(?string $numtlf): self
    {
        $this->numtlf = $numtlf;
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

    public function isApproved(): bool
    {
        return $this->isApproved ?? false;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function getRegistrationRole(): ?string
    {
        return $this->registrationRole;
    }

    public function setRegistrationRole(?string $registrationRole): self
    {
        $this->registrationRole = $registrationRole;
        return $this;
    }

    /**
     * @return Collection<int, Beneficiaire>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Beneficiaire $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->addParent($this);
        }
        return $this;
    }

    public function removeChild(Beneficiaire $child): self
    {
        if ($this->children->removeElement($child)) {
            $child->removeParent($this);
        }
        return $this;
    }
}