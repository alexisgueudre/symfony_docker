<?php

namespace App\Entity;

use App\Repository\UniqueVisitorsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UniqueVisitorsRepository::class)
 */
class UniqueVisitors
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $current_value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentValue(): ?int
    {
        return $this->current_value;
    }

    public function setCurrentValue(?int $current_value): self
    {
        $this->current_value = $current_value;

        return $this;
    }
}
