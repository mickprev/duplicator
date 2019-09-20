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
use MickPrev\Duplicator\Bridge\Symfony\Bundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder_ItReturnsAConfiguration(): void
    {
        $treeBuilder = new TreeBuilder('mick_prev');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('groups_annotation')->cannotBeEmpty()->defaultValue(Groups::class)->end()
            ->end()
        ;

        $this->assertEquals($treeBuilder, (new Configuration())->getConfigTreeBuilder());
    }
}
