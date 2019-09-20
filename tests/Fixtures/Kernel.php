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

namespace MickPrev\Duplicator\Tests\Fixtures;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle;
use MickPrev\Duplicator\Bridge\Symfony\Bundle\MickPrevDuplicatorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Serializer\Annotation\Groups;

/*
 * Test purpose micro-kernel.
 *
 * @author Mick Prev <support@mickprev.fr>
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        $classes = [
            DoctrineBundle::class,
            MickPrevDuplicatorBundle::class,
            FrameworkBundle::class,
            FriendsOfBehatSymfonyExtensionBundle::class,
        ];

        foreach ($classes as $class) {
            yield new $class();
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $c->loadFromExtension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => '%kernel.cache_dir%/db.sqlite',
                'charset' => 'UTF8',
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore',
                'auto_mapping' => true,
                'mappings' => [
                    'MickPrev\Duplicator\Tests\Fixtures\Entity' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/tests/Fixtures/Entity',
                        'is_bundle' => false,
                        'prefix' => 'MickPrev\Duplicator\Tests\Fixtures\Entity',
                    ],
                ],
            ],
        ]);
        $c->loadFromExtension('framework', array_merge(['secret' => 'MickPrevDuplicatorBundle']));
        $c->loadFromExtension('mick_prev_duplicator', ['groups_annotation' => Groups::class]);

        $loader->load(__DIR__.'/Resources/config/services.yml');
    }
}
