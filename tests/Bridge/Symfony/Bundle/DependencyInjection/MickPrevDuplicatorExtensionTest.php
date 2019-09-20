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

namespace MickPrev\Duplicator\Tests\Bridge\Symfony\Bundle\DependencyInjection;

use MickPrev\Duplicator\Annotation\Groups;
use MickPrev\Duplicator\Bridge\Symfony\Bundle\DependencyInjection\MickPrevDuplicatorExtension;
use MickPrev\Duplicator\ClassInfo;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class MickPrevDuplicatorExtensionTest extends TestCase
{
    /** @var ContainerBuilder|ObjectProphecy */
    private $containerBuilderMock;
    /** @var MickPrevDuplicatorExtension */
    private $mickPrevDuplicatorExtension;

    protected function setUp(): void
    {
        $this->containerBuilderMock = $this->prophesize(ContainerBuilder::class);
        $this->mickPrevDuplicatorExtension = new MickPrevDuplicatorExtension();
    }

    public function testLoadConfig_WithoutConfiguration_ItDoesNotLoadAnything(): void
    {
        $this->containerBuilderMock->getDefinition(Argument::type('string'))->shouldNotBeCalled();
        $this->mickPrevDuplicatorExtension->loadConfig([], $this->containerBuilderMock->reveal());
    }

    public function testLoadConfig_WithAGroupsAnnotationClassThatDoesNotExists_ItThrowsAnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "/Invalid/Class" configured to use groups annotation does not exist.');
        $this->containerBuilderMock->getDefinition(Argument::type('string'))->shouldNotBeCalled();
        $this->mickPrevDuplicatorExtension->loadConfig(['groups_annotation' => '/Invalid/Class'], $this->containerBuilderMock->reveal());
    }

    public function testLoadConfig_WithAValidConfiguration_ItLoadsTheConfiguration(): void
    {
        $definitionMock = $this->prophesize(Definition::class);
        $definitionMock->addMethodCall('setGroupsAnnotation', [Groups::class])->shouldBeCalledOnce();
        $this->containerBuilderMock->getDefinition(ClassInfo::class)->willReturn($definitionMock->reveal())->shouldBeCalledOnce();
        $this->mickPrevDuplicatorExtension->loadConfig(['groups_annotation' => Groups::class], $this->containerBuilderMock->reveal());
    }
}
