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

namespace MickPrev\Duplicator\Tests;

use MickPrev\Duplicator\DefaultDuplicator;
use PHPUnit\Framework\TestCase;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class DefaultDuplicatorTest extends TestCase
{
    /** @var DefaultDuplicator */
    private $defaultDuplicator;

    protected function setUp(): void
    {
        $this->defaultDuplicator = new DefaultDuplicator();
    }

    /**
     * @dataProvider valueProvider
     */
    public function testDuplicate_ItReturnsTheArgumentValue($value): void
    {
        $this->assertSame($value, $this->defaultDuplicator->duplicate($value));
    }

    /**
     * @dataProvider valueProvider
     */
    public function testSupports_ItReturnsTrue($value): void
    {
        $this->assertTrue($this->defaultDuplicator->supports($value));
    }

    public function valueProvider(): array
    {
        return [
            'array' => [['array']],
            'decimal' => [1.0],
            'integer' => [1],
            'object' => [new \stdClass()],
            'string' => ['string'],
        ];
    }
}
