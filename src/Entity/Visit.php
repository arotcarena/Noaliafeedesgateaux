<?php

namespace App\Entity;

use App\Repository\VisitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitRepository::class)]
class Visit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $visitedPageId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $visitedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitedAt(): ?\DateTimeImmutable
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeImmutable $visitedAt): self
    {
        $this->visitedAt = $visitedAt;

        return $this;
    }

    public function getVisitedPageId(): ?string
    {
        return $this->visitedPageId;
    }

    public function setVisitedPageId(string $visitedPageId): self
    {
        $this->visitedPageId = $visitedPageId;

        return $this;
    }
}
