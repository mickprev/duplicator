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

namespace MickPrev\Duplicator;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
class IterableDuplicator implements DuplicatorInterface, DuplicatorAwareInterface
{
    use DuplicatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function duplicate($value, array $context = []): iterable
    {
        $items = [];
        foreach ($value as $key => $originalItem) {
            if (null !== ($item = $this->duplicator->duplicate($originalItem, $context))) {
                $items[$key] = $item;
            }
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($value, array $context = []): bool
    {
        return \is_iterable($value);
    }
}
