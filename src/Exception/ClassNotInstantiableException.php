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

namespace MickPrev\Duplicator\Exception;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class ClassNotInstantiableException extends \RuntimeException
{
    public function __construct(string $class)
    {
        parent::__construct("Class $class can not be instantiated. Can be instantiated only classes without argument or with default values.");
    }
}
