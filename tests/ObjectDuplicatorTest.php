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

use MickPrev\Duplicator\ClassInfoInterface;
use MickPrev\Duplicator\DuplicatorInterface;
use MickPrev\Duplicator\Exception\RootObjectImmutableException;
use MickPrev\Duplicator\ObjectDuplicator;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ObjectDuplicatorTest extends TestCase
{
    /** @var ClassInfoInterface|ObjectProphecy */
    private $classInfoMock;
    /** @var PropertyAccessorInterface|ObjectProphecy */
    private $propertyAccessorMock;
    /** @var ObjectDuplicator */
    private $objectDuplicator;

    protected function setUp(): void
    {
        $this->classInfoMock = $this->prophesize(ClassInfoInterface::class);
        $this->propertyAccessorMock = $this->prophesize(PropertyAccessorInterface::class);
        $this->objectDuplicator = new ObjectDuplicator($this->classInfoMock->reveal(), $this->propertyAccessorMock->reveal());
    }

    public function testSupports_WithAnObjectAsValue_ItReturnsTrue(): void
    {
        $this->assertTrue($this->objectDuplicator->supports(new \stdClass()));
    }

    /**
     * @dataProvider notSupportedValuesProvider
     */
    public function testSupports_ItReturnsFalse($value): void
    {
        $this->assertFalse($this->objectDuplicator->supports($value));
    }

    public function notSupportedValuesProvider(): array
    {
        return [
            'array' => [['test']],
            'float' => [10.10],
            'integer' => [10],
            'string' => ['test'],
        ];
    }

    public function testDuplicate_WithARootObjectAndNoPropertyToDuplicate_ItThrowsAnException(): void
    {
        $productMock = $this->prophesize(Product::class);
        $context = ['depth' => 0];
        $this->classInfoMock->getProperties($productMock->reveal(), $context)->willReturn([])->shouldBeCalledOnce();
        $this->expectException(RootObjectImmutableException::class);
        $this->expectExceptionMessage('A root object cannot be immutable.');
        $this->objectDuplicator->duplicate($productMock->reveal(), $context);
    }

    public function testDuplicate_WithANotRootObjectAndNoPropertyToDuplicate_ItReturnsTheObjectInParameter(): void
    {
        $productMock = $this->prophesize(Product::class);
        $context = ['depth' => 1];
        $this->classInfoMock->getProperties($productMock->reveal(), $context)->willReturn([])->shouldBeCalledOnce();
        $this->assertEquals(
            $productMock->reveal(),
            $this->objectDuplicator->duplicate($productMock->reveal(), $context)
        );
    }

    public function testDuplicate_WithPropertiesToDuplicate_ItReturnsANewObject(): void
    {
        $originalProductMock = $this->prophesize(Product::class);
        $newProductMock = $this->prophesize(Product::class);
        $context = ['depth' => 1, 'groups' => 'duplicate_product_advice'];

        $titlePropertyMock = $this->prophesize('ReflectionProperty');
        $titlePropertyMock->getName()->willReturn('title')->shouldBeCalledOnce();

        $descriptionPropertyMock = $this->prophesize('ReflectionProperty');
        $descriptionPropertyMock->getName()->willReturn('description')->shouldBeCalledOnce();

        $this->classInfoMock->getProperties($originalProductMock->reveal(), $context)
            ->willReturn([$titlePropertyMock->reveal(), $descriptionPropertyMock->reveal()])
            ->shouldBeCalledOnce();
        $this->classInfoMock->newInstance($originalProductMock->reveal())
            ->willReturn($newProductMock->reveal())
            ->shouldBeCalledOnce();

        $chainDuplicatorMock = $this->prophesize(DuplicatorInterface::class);
        $chainDuplicatorMock->duplicate('Title', $context + ['currentObject' => $originalProductMock->reveal(), 'propertyPath' => 'title'])
            ->willReturn('Title')
            ->shouldBeCalledOnce();
        $chainDuplicatorMock->duplicate('Description', $context + ['currentObject' => $originalProductMock->reveal(), 'propertyPath' => 'description'])
            ->willReturn('Description')
            ->shouldBeCalledOnce();

        $this->propertyAccessorMock->isReadable($originalProductMock->reveal(), 'title')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyAccessorMock->isWritable($newProductMock->reveal(), 'title')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyAccessorMock->getValue($originalProductMock->reveal(), 'title')->willReturn('Title')->shouldBeCalledOnce();
        $this->propertyAccessorMock->setValue($newProductMock->reveal(), 'title', 'Title')->shouldBeCalledOnce();

        $this->propertyAccessorMock->isReadable($originalProductMock->reveal(), 'description')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyAccessorMock->isWritable($newProductMock->reveal(), 'description')->willReturn(true)->shouldBeCalledOnce();
        $this->propertyAccessorMock->getValue($originalProductMock->reveal(), 'description')->willReturn('Description')->shouldBeCalledOnce();
        $this->propertyAccessorMock->setValue($newProductMock->reveal(), 'description', 'Description')->shouldBeCalledOnce();

        $this->objectDuplicator->setDuplicator($chainDuplicatorMock->reveal());

        $this->assertEquals(
            $newProductMock->reveal(),
            $this->objectDuplicator->duplicate($originalProductMock->reveal(), $context)
        );
    }
}
