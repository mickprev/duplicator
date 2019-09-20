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

namespace MickPrev\Duplicator\Tests\Fixtures\Duplicator;

use MickPrev\Duplicator\DuplicatorAwareInterface;
use MickPrev\Duplicator\DuplicatorAwareTrait;
use MickPrev\Duplicator\DuplicatorInterface;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Advantage;

final class AdvantageDuplicator implements DuplicatorInterface, DuplicatorAwareInterface
{
    private const ALREADY_CALLED = 'advantage_duplicator_already_called';

    use DuplicatorAwareTrait;

    /**
     * @param Advantage $value
     */
    public function duplicate($value, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        return $value->common ? $this->duplicator->duplicate($value, $context) : null;
    }

    public function supports($value, array $context = []): bool
    {
        return \is_object($value) && $value instanceof Advantage && !isset($context[self::ALREADY_CALLED]);
    }
}
