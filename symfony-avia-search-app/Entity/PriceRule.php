<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PriceRuleRepository")
 */
class PriceRule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, options={"default":""})
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $fixed = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $percent = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $rule = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commission", mappedBy="priceRule", fetch="EXTRA_LAZY")
     */
    private Collection $commission;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFixed(): ?int
    {
        return $this->fixed;
    }

    public function setFixed(int $fixed): self
    {
        $this->fixed = $fixed;

        return $this;
    }

    public function getPercent(): ?int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(?string $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @return Collection|Commission[]
     */
    public function getCommission(): Collection
    {
        return $this->commission;
    }

//    public function addCommission(Commission $commission): self
//    {
//        if (!$this->commission->contains($commission)) {
//            $this->commission[] = $commission;
//            $commission->setPriceRule($this);
//        }
//
//        return $this;
//    }
//
//    public function removeCommission(Commission $commission): self
//    {
//        if ($this->commission->contains($commission)) {
//            $this->commission->removeElement($commission);
//            // set the owning side to null (unless already changed)
//            if ($commission->getPriceRule() === $this) {
//                $commission->setPriceRule(null);
//            }
//        }
//
//        return $this;
//    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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

    public function setCommission(Collection $commission): void
    {
        /** @var Commission $item */
        foreach ($commission as $item) {
            $item->setPriceRule($this);
        }

        $this->commission = $commission;
    }
}
