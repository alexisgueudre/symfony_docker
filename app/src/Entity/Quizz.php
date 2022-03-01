<?php

namespace App\Entity;

use App\Repository\QuizzRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizzRepository::class)
 */
class Quizz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="quizz")
     */
    private $id_category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="quizz", cascade={"remove"})
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="quizz", cascade={"remove"})
     */
    private $histories;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="quizzs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function __construct()
    {
        $this->id_category = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Category[]
     */
    public function getIdCategory(): Collection
    {
        return $this->id_category;
    }

    public function addIdCategory(Category $idCategory): self
    {
        if (!$this->id_category->contains($idCategory)) {
            $this->id_category[] = $idCategory;
            $idCategory->setQuizz($this);
        }

        return $this;
    }

    public function removeIdCategory(Category $idCategory): self
    {
        if ($this->id_category->removeElement($idCategory)) {
            // set the owning side to null (unless already changed)
            if ($idCategory->getQuizz() === $this) {
                $idCategory->setQuizz(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->question->contains($question)) {
            $this->question[] = $question;
            $question->setIdQuizz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->question->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getIdQuizz() === $this) {
                $question->setIdQuizz(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setIdQuizz($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getIdQuizz() === $this) {
                $history->setIdQuizz(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param ArrayCollection $id_category
     */
    public function setIdCategory(ArrayCollection $id_category): void
    {
        $this->id_category = $id_category;
    }
    public function __toString()
    {
        return $this->id." ".$this->getName()." ".$this->getCategory();
    }
}
