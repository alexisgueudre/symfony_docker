<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Quizz::class, inversedBy="question")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quizz;

    /**
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question", cascade={"remove"})
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswers()
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setIdQuestion($this);
        }

        return $this;
    }

    public function setAnswers($array) {
        return $this->answers = $array;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getIdQuestion() === $this) {
                $answer->setIdQuestion(null);
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuizz()
    {
        return $this->quizz;
    }

    /**
     * @param mixed $quizz
     */
    public function setQuizz($quizz): void
    {
        $this->quizz = $quizz;
    }
}
