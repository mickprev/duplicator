<?php

/*
 * This file is part of the duplicator project.
 *
 * (c) Mick Prev <support@mickprev.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MickPrev\Duplicator\Tests\Fixtures\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MickPrev\Duplicator\Annotation\Groups as DuplicatorGroups;
use MickPrev\Duplicator\Tests\Fixtures\Doctrine\ORM\AppUuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups as SerializationGroups;

/**
 * @ORM\Entity
 *
 * @author Mick Prev <support@mickprev.fr>
 */
class Product
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\CustomIdGenerator(class=AppUuidGenerator::class)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     * @DuplicatorGroups({"duplicate", "duplicate_product_advice"})
     * @SerializationGroups({"write_product", "write_product_advice"})
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @DuplicatorGroups({"duplicate", "duplicate_product_advice"})
     * @SerializationGroups({"write_product", "write_product_advice"})
     */
    public $description;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity=Category::class))
     * @DuplicatorGroups({"duplicate"})
     * @SerializationGroups({"write_product"})
     */
    public $category;

    /**
     * @var Collection|Advantage[]
     *
     * @ORM\ManyToMany(targetEntity=Advantage::class, cascade={"persist"})
     * @DuplicatorGroups({"duplicate"})
     * @SerializationGroups({"write_product"})
     */
    public $advantages;

    /**
     * @var Collection|Advice[]
     *
     * @ORM\OneToMany(targetEntity=Advice::class, mappedBy="product", cascade={"persist"})
     * @DuplicatorGroups({"duplicate_advice"})
     * @SerializationGroups({"write_product", "write_product_advice"})
     */
    public $advices;

    /**
     * @var Collection|TechnicalInformation[]
     *
     * @ORM\OneToMany(targetEntity=TechnicalInformation::class, mappedBy="product", cascade={"persist"})
     * @DuplicatorGroups({"duplicate"})
     * @SerializationGroups({"write_product"})
     */
    public $technicalInformations;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity=Person::class))
     */
    public $author;

    public function __construct()
    {
        $this->advantages = new ArrayCollection();
        $this->advices = new ArrayCollection();
        $this->technicalInformations = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function addAdvantage(Advantage $advantage): void
    {
        $this->advantages[] = $advantage;
    }

    public function removeAdvantage(Advantage $advantage): void
    {
        $this->advantages->removeElement($advantage);
    }

    public function addAdvice(Advice $advice): void
    {
        $this->advices[] = $advice;
        $advice->product = $this;
    }

    public function removeAdvice(Advice $advice): void
    {
        $this->advices->removeElement($advice);
    }

    public function addTechnicalInformation(TechnicalInformation $technicalInformation): void
    {
        $this->technicalInformations[] = $technicalInformation;
        $technicalInformation->product = $this;
    }

    public function removeTechnicalInformation(TechnicalInformation $technicalInformation): void
    {
        $this->technicalInformations->removeElement($technicalInformation);
    }
}
