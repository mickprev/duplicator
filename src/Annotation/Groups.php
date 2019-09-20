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

namespace MickPrev\Duplicator\Annotation;

use MickPrev\Duplicator\Exception\InvalidArgumentException;

/**
 * Annotation class for @Groups.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Mick Prev <support@mickprev.fr>
 */
class Groups
{
    /** @var string[] */
    private $groups;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) || !$data['value']) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.', __CLASS__));
        }

        $value = (array) $data['value'];
        foreach ($value as $group) {
            if (!\is_string($group)) {
                throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" must be a string or an array of strings.', __CLASS__));
            }
        }

        $this->groups = $value;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}
