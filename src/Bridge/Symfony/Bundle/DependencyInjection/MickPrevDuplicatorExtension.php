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

namespace MickPrev\Duplicator\Bridge\Symfony\Bundle\DependencyInjection;

use MickPrev\Duplicator\ClassInfo;
use MickPrev\Duplicator\DuplicatorInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class MickPrevDuplicatorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->registerForAutoconfiguration(DuplicatorInterface::class)->addTag('mick_prev.duplicator');

        (new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config')))->load('duplicator.xml');

        $this->loadConfig($config, $container);
    }

    public function loadConfig(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['groups_annotation'])) {
            return;
        }

        if (!\class_exists($config['groups_annotation'])) {
            throw new \InvalidArgumentException("Class \"$config[groups_annotation]\" configured to use groups annotation does not exist.");
        }

        $container->getDefinition(ClassInfo::class)->addMethodCall('setGroupsAnnotation', [$config['groups_annotation']]);
    }
}
