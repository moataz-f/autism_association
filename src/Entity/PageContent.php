<?php
// src/Entity/PageContent.php
namespace App\Entity;

use App\Repository\PageContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PageContentRepository::class)]
#[Vich\Uploadable]
class PageContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $sectionKey = null; // 'hero', 'about', 'stats', etc.

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sousTitre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoPath = null;

    #[Vich\UploadableField(mapping: 'hero_videos', fileNameProperty: 'videoPath')]
    private ?File $videoFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[Vich\UploadableField(mapping: 'page_images', fileNameProperty: 'imagePath')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonLink = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $extraData = null; // Pour données supplémentaires

    #[ORM\Column]
    private ?bool $actif = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSectionKey(): ?string
    {
        return $this->sectionKey;
    }

    public function setSectionKey(string $sectionKey): self
    {
        $this->sectionKey = $sectionKey;
        return $this;
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

    public function getSousTitre(): ?string
    {
        return $this->sousTitre;
    }

    public function setSousTitre(?string $sousTitre): self
    {
        $this->sousTitre = $sousTitre;
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

    public function getVideoPath(): ?string
    {
        return $this->videoPath;
    }

    public function setVideoPath(?string $videoPath): self
    {
        $this->videoPath = $videoPath;
        return $this;
    }

    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    public function setVideoFile(?File $videoFile = null): self
    {
        $this->videoFile = $videoFile;
        if ($videoFile) {
            $this->updatedAt = new \DateTime();
        }
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->updatedAt = new \DateTime();
        }
        return $this;
    }

    public function getButtonText(): ?string
    {
        return $this->buttonText;
    }

    public function setButtonText(?string $buttonText): self
    {
        $this->buttonText = $buttonText;
        return $this;
    }

    public function getButtonLink(): ?string
    {
        return $this->buttonLink;
    }

    public function setButtonLink(?string $buttonLink): self
    {
        $this->buttonLink = $buttonLink;
        return $this;
    }

    public function getExtraData(): ?array
    {
        return $this->extraData;
    }

    public function setExtraData(?array $extraData): self
    {
        $this->extraData = $extraData;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
