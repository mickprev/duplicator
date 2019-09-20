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
use MickPrev\Duplicator\Tests\Fixtures\Doctrine\ORM\AppUuidGenerator;

/**
 * @ORM\Entity
 *
 * @author Mick Prev <support@mickprev.fr>
 */
class Advantage
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
     */
    public $icon;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    public $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_common")
     */
    public $common = true;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
