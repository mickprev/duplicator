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
interface DuplicatorInterface
{
    public function duplicate($value, array $context = []);

    public function supports($value, array $context = []): bool;
}
