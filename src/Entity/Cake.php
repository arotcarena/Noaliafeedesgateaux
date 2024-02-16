<?php

namespace App\Entity;

use Exception;
use App\Config;
use App\Entity\Picture;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CakeRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use App\Exception\FormException\UploadException;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CakeRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Cake
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'cake', targetEntity: Picture::class, cascade: ['persist', 'remove'])]
    private Collection $pictures;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Assert\File(
        maxSize: '2000k',
        maxSizeMessage: '{{ name }} fait {{ size }}Mo, le maximum est de {{ limit }}Mo',
    )]
    #[Assert\Image(
        mimeTypes: ['image/jpeg'],
        mimeTypesMessage: '{{ name }} n\'est pas au bon format : format jpeg ou png exigé',
    )]
    private ?File $firstPictureFile = null;

    #[Assert\All(
        [
            new Assert\File(
            maxSize: '2000k',
            maxSizeMessage: '{{ name }} fait {{ size }}Mo, le maximum est de {{ limit }}Mo'
            ),
            new Assert\Image(
                mimeTypes: ['image/jpeg', 'image/png'],
                mimeTypesMessage: '{{ name }} n\'est pas au bon format : format jpeg ou png exigé'
            )
        ]
    )]
    private $pictureFiles;

    #[ORM\Column]
    private bool $spotlighted = false;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Picture $firstPicture = null;

    #[ORM\Column(nullable: true)]
    private ?int $countVisit = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastVisit = null;


    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function addVisit()
    {
        $this->countVisit ++;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }


    public function getFirstPictureFile()
    {
        return $this->firstPictureFile;
    }

    public function setFirstPictureFile(File $file)
    {
        $picture = new Picture;
        $picture->setFile($file);
        $this->setFirstPicture($picture);
        $this->firstPictureFile = $file;
    }

    public function getPictureFiles()
    {
        return $this->pictureFiles;
    }

    /**
     * @param File[] $files
     * @return void
     */
    public function setPictureFiles($files)
    {
        if(count($files) > Config::MAX_PICTURES_ON_SAME_UPLOAD)
        {
            throw new UploadException(Config::MAX_PICTURES_ON_SAME_UPLOAD_MESSAGE, 'pictureFiles');
        }
        if(count($files) + count($this->getPictures()) >= (Config::MAX_PICTURES_PER_CAKE - 1))
        {
            throw new UploadException(Config::MAX_PICTURES_PER_CAKE_MESSAGE, 'pictureFiles');
        }
        foreach($files as $file)
        {
            $picture = new Picture;
            $picture->setFile($file);
            $this->addPicture($picture);
        }
        $this->pictureFiles = $files;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setCake($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getCake() === $this) {
                $picture->setCake(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }



    public function isSpotlighted(): ?bool
    {
        return $this->spotlighted;
    }

    public function setSpotlighted(bool $spotlighted): self
    {
        $this->spotlighted = $spotlighted;

        return $this;
    }

    public function getFirstPicture(): ?Picture
    {
        return $this->firstPicture;
    }

    public function setFirstPicture(Picture $firstPicture): self
    {
        $this->firstPicture = $firstPicture;

        return $this;
    }

    public function getCountVisit(): ?int
    {
        return $this->countVisit;
    }

    public function setCountVisit(?int $countVisit): self
    {
        $this->countVisit = $countVisit;

        return $this;
    }

    public function getLastVisit(): ?\DateTimeImmutable
    {
        return $this->lastVisit;
    }

    public function setLastVisit(?\DateTimeImmutable $lastVisit): self
    {
        $this->lastVisit = $lastVisit;

        return $this;
    }

    
}

