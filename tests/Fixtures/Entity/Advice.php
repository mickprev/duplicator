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

use Doctrine\ORM\Mapping as ORM;
use MickPrev\Duplicator\Annotation\Groups as DuplicatorGroups;
use MickPrev\Duplicator\Tests\Fixtures\Doctrine\ORM\AppUuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups as SerializationGroups;

/**
 * @ORM\Entity
 *
 * @author Mick Prev <support@mickprev.fr>
 */
class Advice
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
     * @DuplicatorGroups({"duplicate_advice"})
     * @SerializationGroups({"write_advice"})
     */
    public $image;

    /**
     * @var string
     *
     * @ORM\Column
     * @DuplicatorGroups({"duplicate_advice"})
     * @SerializationGroups({"write_advice"})
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @DuplicatorGroups({"duplicate_advice"})
     * @SerializationGroups({"write_advice"})
     */
    public $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @DuplicatorGroups({"duplicate_advice"})
     * @SerializationGroups({"write_advice"})
     */
    public $description;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="advices")
     */
    public $product;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
