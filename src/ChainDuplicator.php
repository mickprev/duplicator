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

use MickPrev\Duplicator\Exception\MaxDepthException;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class ChainDuplicator implements DuplicatorInterface
{
    private const DEFAULT_MAX_DEPTH = 128;

    /** @var iterable|DuplicatorInterface[] */
    private $duplicators;

    public function __construct(iterable $duplicators)
    {
        foreach ($duplicators as $duplicator) {
            if ($duplicator instanceof DuplicatorAwareInterface) {
                $duplicator->setDuplicator($this);
            }
        }

        $this->duplicators = $duplicators;
    }

    /**
     * {@inheritdoc}
     */
    public function duplicate($value, array $context = [])
    {
        $context += [
            'max_depth' => self::DEFAULT_MAX_DEPTH,
        ];

        $context['depth'] = isset($context['depth']) ? ++$context['depth'] : 0;

        if ($context['depth'] === $context['max_depth']) {
            throw new MaxDepthException();
        }

        foreach ($this->duplicators as $duplicator) {
            if ($duplicator->supports($value, $context)) {
                return $duplicator->duplicate($value, $context);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($value, array $context = []): bool
    {
        foreach ($this->duplicators as $duplicator) {
            if ($duplicator->supports($value, $context)) {
                return true;
            }
        }

        return false;
    }
}
