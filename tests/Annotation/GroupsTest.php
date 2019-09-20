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

namespace MickPrev\Duplicator\Tests\Annotation;

use MickPrev\Duplicator\Annotation\Groups;
use MickPrev\Duplicator\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class GroupsTest extends TestCase
{
    public function testConstruct_WithAnEmptyArgument_ItThrowsAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter of annotation "MickPrev\Duplicator\Annotation\Groups" cannot be empty.');
        new Groups([]);
    }

    public function testConstruct_WithAValueThatIsNotAString_ItThrowsAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter of annotation "MickPrev\Duplicator\Annotation\Groups" must be a string or an array of strings.');
        new Groups(['value' => 67]);
    }

    /**
     * @dataProvider valueProvider
     */
    public function testGetGroups_ItReturnsAnArray($value): void
    {
        $annotation = new Groups(['value' => $value]);
        $this->assertEquals((array) $value, $annotation->getGroups());
    }

    public function valueProvider(): array
    {
        return [
            'array' => [['duplicate_product']],
            'string' => ['duplicate_product'],
        ];
    }
}
