<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="category")
     */
    private $aricles;

    public function __construct()
    {
        $this->aricles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|Article[]
     */
    public function getAricles(): Collection
    {
        return $this->aricles;
    }

    public function addAricle(Article $aricle): self
    {
        if (!$this->aricles->contains($aricle)) {
            $this->aricles[] = $aricle;
            $aricle->setCategory($this);
        }

        return $this;
    }

    public function removeAricle(Article $aricle): self
    {
        if ($this->aricles->removeElement($aricle)) {
            // set the owning side to null (unless already changed)
            if ($aricle->getCategory() === $this) {
                $aricle->setCategory(null);
            }
        }

        return $this;
    }
}
