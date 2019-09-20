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

use Doctrine\Common\Collections\ArrayCollection;
use MickPrev\Duplicator\DuplicatorInterface;
use MickPrev\Duplicator\IterableDuplicator;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Advantage;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;
use PHPUnit\Framework\TestCase;

class IterableDuplicatorTest extends TestCase
{
    /** @var IterableDuplicator */
    private $iterableDuplicator;

    protected function setUp(): void
    {
        $this->iterableDuplicator = new IterableDuplicator();
    }

    /**
     * @dataProvider supportedValuesProvider
     */
    public function testSupports_ItReturnsTrue($value): void
    {
        $this->assertTrue($this->iterableDuplicator->supports($value));
    }

    public function supportedValuesProvider(): array
    {
        return [
            'array' => [['test']],
            'traversable object' => [new ArrayCollection()],
        ];
    }

    /**
     * @dataProvider notSupportedValuesProvider
     */
    public function testSupports_ItReturnsFalse($value): void
    {
        $this->assertFalse($this->iterableDuplicator->supports($value));
    }

    public function notSupportedValuesProvider(): array
    {
        return [
            'float' => [10.10],
            'integer' => [10],
            'string' => ['test'],
            'non-traversable object' => [Product::class],
        ];
    }

    public function testDuplicate_WithItemsToDuplicate_ItReturnsNewItems(): void
    {
        $context = [];

        $originalAdvantageEasyBreathingMock = $this->prophesize(Advantage::class);
        $originalAdvantageAntiFoggingMock = $this->prophesize(Advantage::class);

        $newAdvantageEasyBreathingMock = $this->prophesize(Advantage::class);
        $newAdvantageAntiFoggingMock = $this->prophesize(Advantage::class);

        $originalAdvantages = new ArrayCollection([
            $originalAdvantageEasyBreathingMock->reveal(),
            $originalAdvantageAntiFoggingMock->reveal(),
        ]);

        $chainDuplicatorMock = $this->prophesize(DuplicatorInterface::class);

        $chainDuplicatorMock->duplicate($originalAdvantageEasyBreathingMock->reveal(), $context)
            ->willReturn($newAdvantageEasyBreathingMock->reveal())
            ->shouldBeCalledOnce();

        $chainDuplicatorMock->duplicate($originalAdvantageAntiFoggingMock->reveal(), $context)
            ->willReturn($newAdvantageAntiFoggingMock->reveal())
            ->shouldBeCalledOnce();

        $this->iterableDuplicator->setDuplicator($chainDuplicatorMock->reveal());

        $this->assertEquals(
            [
                $newAdvantageEasyBreathingMock->reveal(),
                $newAdvantageAntiFoggingMock->reveal(),
            ],
            $this->iterableDuplicator->duplicate($originalAdvantages, $context)
        );
    }
}
