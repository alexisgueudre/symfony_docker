<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="histories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Quizz::class, inversedBy="histories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quizz;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $score;

    /**
     * @ORM\Column(type="datetime")
     */
    private $played_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->user;
    }

    public function setIdUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdQuizz(): ?Quizz
    {
        return $this->quizz;
    }

    public function setIdQuizz(?Quizz $quizz): self
    {
        $this->quizz = $quizz;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getPlayedAt(): ?\DateTimeInterface
    {
        return $this->played_at;
    }

    public function setPlayedAt(\DateTimeInterface $played_at): self
    {
        $this->played_at = $played_at;

        return $this;
    }
}
