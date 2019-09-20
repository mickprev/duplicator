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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use MickPrev\Duplicator\Annotation\Groups;
use MickPrev\Duplicator\ChainDuplicator;
use MickPrev\Duplicator\ClassInfo;
use MickPrev\Duplicator\Exception\ClassNotInstantiableException;
use MickPrev\Duplicator\Exception\MissingGroupsContextException;
use MickPrev\Duplicator\Exception\RuntimeException;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ClassInfoTest extends TestCase
{
    public function testGetReader_WithoutReader_ItReturnsAReader(): void
    {
        $this->assertEquals(new AnnotationReader(), (new ClassInfo())->getReader());
    }

    public function testSetGroupsAnnotation_WithAnUnknownClass_ItThrowsAnException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class Class\That\Does\Not\Exist does not exist.');
        (new ClassInfo())->setGroupsAnnotation('Class\That\Does\Not\Exist');
    }

    public function testSetGroupsAnnotation_WithAClassWithoutGetGroupsMethod_ItThrowsAnException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class stdClass must implement the accessible getGroups method.');
        (new ClassInfo())->setGroupsAnnotation('stdClass');
    }

    public function testNewInstance_WithANotInstantiableClass_ItThrowsAnException(): void
    {
        $this->expectException(ClassNotInstantiableException::class);
        $this->expectExceptionMessage('Class MickPrev\Duplicator\ChainDuplicator can not be instantiated. Can be instantiated only classes without argument or with default values.');
        (new ClassInfo())->newInstance(ChainDuplicator::class);
    }

    public function testNewInstance_WithAnInstantiableClass_ItReturnsANewInstance(): void
    {
        $productMock = $this->prophesize(Product::class);
        $this->assertNotEquals($productMock->reveal(), (new ClassInfo())->newInstance($productMock->reveal()));
    }

    public function testGetProperties_WithoutGroupsInContext_ItThrowsAnException(): void
    {
        $this->expectException(MissingGroupsContextException::class);
        $this->expectExceptionMessage('At least one group is required in the context.');
        (new ClassInfo())->getProperties(Product::class, []);
    }

    public function testGetProperties_WithAValidContextAndGroupsThatMatches_ItReturnsProperties(): void
    {
        $titleProperty = new \ReflectionProperty(Product::class, 'title');
        $descriptionProperty = new \ReflectionProperty(Product::class, 'description');

        $groups = $this->prophesize(Groups::class);
        $groups->getGroups()->willReturn(['duplicate', 'duplicate_product_advice'])->shouldBeCalledTimes(2);

        $readerMock = $this->prophesize(Reader::class);
        $readerMock->getPropertyAnnotation($titleProperty, Groups::class)->willReturn($groups)->shouldBeCalledOnce();
        $readerMock->getPropertyAnnotation($descriptionProperty, Groups::class)->willReturn($groups)->shouldBeCalledOnce();
        $readerMock->getPropertyAnnotation(Argument::type(\ReflectionProperty::class), Groups::class)->willReturn([])->shouldBeCalledTimes(8);

        $this->assertEquals(
            [$titleProperty, $descriptionProperty],
            (new ClassInfo($readerMock->reveal()))->getProperties(Product::class, ['groups' => 'duplicate_product_advice'])
        );
    }
}
