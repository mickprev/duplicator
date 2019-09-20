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

use MickPrev\Duplicator\ChainDuplicator;
use MickPrev\Duplicator\DefaultDuplicator;
use MickPrev\Duplicator\Exception\MaxDepthException;
use MickPrev\Duplicator\IterableDuplicator;
use MickPrev\Duplicator\ObjectDuplicator;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class ChainDuplicatorTest extends TestCase
{
    /** @var IterableDuplicator|ObjectProphecy */
    private $iterableDuplicatorMock;
    /** @var ObjectDuplicator|ObjectProphecy */
    private $objectDuplicatorMock;
    /** @var DefaultDuplicator|ObjectProphecy */
    private $defaultDuplicatorMock;
    /** @var ChainDuplicator */
    private $chainDuplicator;

    protected function setUp(): void
    {
        $this->iterableDuplicatorMock = $this->prophesize(IterableDuplicator::class);
        $this->objectDuplicatorMock = $this->prophesize(ObjectDuplicator::class);
        $this->defaultDuplicatorMock = $this->prophesize(DefaultDuplicator::class);

        $this->chainDuplicator = new ChainDuplicator([
            $this->iterableDuplicatorMock->reveal(),
            $this->objectDuplicatorMock->reveal(),
            $this->defaultDuplicatorMock->reveal(),
        ]);

        $this->iterableDuplicatorMock->setDuplicator($this->chainDuplicator)->shouldBeCalledOnce();
        $this->objectDuplicatorMock->setDuplicator($this->chainDuplicator)->shouldBeCalledOnce();
    }

    public function testDuplicate_WithDepth0_ItReturnsAnObject(): void
    {
        $productMock = $this->prophesize(Product::class);
        $newProductMock = $this->prophesize(Product::class);
        $this->iterableDuplicatorMock->supports($productMock->reveal(), ['depth' => 0, 'max_depth' => 128])
            ->willReturn(false)
            ->shouldBeCalledOnce();
        $this->objectDuplicatorMock->supports($productMock->reveal(), ['depth' => 0, 'max_depth' => 128])
            ->willReturn(true)
            ->shouldBeCalledOnce();
        $this->objectDuplicatorMock->duplicate($productMock->reveal(), ['depth' => 0, 'max_depth' => 128])
            ->willReturn($newProductMock->reveal())
            ->shouldBeCalledOnce();
        $this->assertEquals($newProductMock->reveal(), $this->chainDuplicator->duplicate($productMock->reveal()));
    }

    public function testDuplicate_WithDepth1_ItReturnsAnObject(): void
    {
        $title = 'Easybreath';
        $this->iterableDuplicatorMock->supports($title, ['depth' => 1, 'max_depth' => 128])
            ->willReturn(false)
            ->shouldBeCalledOnce();
        $this->objectDuplicatorMock->supports($title, ['depth' => 1, 'max_depth' => 128])
            ->willReturn(false)
            ->shouldBeCalledOnce();
        $this->defaultDuplicatorMock->supports($title, ['depth' => 1, 'max_depth' => 128])
            ->willReturn(true)
            ->shouldBeCalledOnce();
        $this->defaultDuplicatorMock->duplicate($title, ['depth' => 1, 'max_depth' => 128])
            ->willReturn($title)
            ->shouldBeCalledOnce();
        $this->assertEquals($title, $this->chainDuplicator->duplicate($title, ['depth' => 0]));
    }

    public function testDuplicate_WithReachedMaxDepth_ItThrowsAnException(): void
    {
        $this->expectException(MaxDepthException::class);
        $this->expectExceptionMessage('The maximum depth has been reached.');
        $this->assertEquals('Easybreath', $this->chainDuplicator->duplicate('Easybreath', ['depth' => 63, 'max_depth' => 64]));
    }

    public function testSupports_WithADuplicatorThatSupports_ItReturnsTue(): void
    {
        $this->iterableDuplicatorMock->supports(new \stdClass(), ['groups' => ['duplicate']])->willReturn(false)->shouldBeCalledOnce();
        $this->objectDuplicatorMock->supports(new \stdClass(), ['groups' => ['duplicate']])->willReturn(true)->shouldBeCalledOnce();
        $this->defaultDuplicatorMock->supports(new \stdClass(), ['groups' => ['duplicate']])->willReturn(true)->shouldNotBeCalled();
        $this->assertTrue($this->chainDuplicator->supports(new \stdClass(), ['groups' => ['duplicate']]));
    }

    public function testSupports_WithoutDuplicatorThatSupports_ItReturnsFalse(): void
    {
        $this->iterableDuplicatorMock->supports('', [])->willReturn(false)->shouldBeCalledOnce();
        $this->objectDuplicatorMock->supports('', [])->willReturn(false)->shouldBeCalledOnce();
        $this->defaultDuplicatorMock->supports('', [])->willReturn(false)->shouldBeCalledOnce();
        $this->assertFalse($this->chainDuplicator->supports(''));
    }
}
