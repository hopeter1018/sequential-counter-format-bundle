<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\DependencyInjection;

use HoPeter1018\SequentialCounterFormatBundle\Annotations\ClassRule;
use HoPeter1018\SequentialCounterFormatBundle\Annotations\PropertyRule;
use HoPeter1018\SequentialCounterFormatBundle\CacheWarm\MappingCacheWarmUp;
use HoPeter1018\SequentialCounterFormatBundle\Services\SequentialCounterFormatter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class HoPeter1018SequentialCounterFormatExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $serviceDefintion = $container->getDefinition(SequentialCounterFormatter::class);
        $serviceDefintion->setArgument(0, $config);

        $serviceDefintion = $container->getDefinition(MappingCacheWarmUp::class);
        $serviceDefintion->setArgument(0, $config);

        $this->addAnnotatedClassesToCompile([
            ClassRule::class,
            PropertyRule::class,
        ]);
    }
}
