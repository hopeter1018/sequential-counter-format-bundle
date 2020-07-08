<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ho_peter1018_sequential_counter_format');

        $rootNode
            ->children()
                ->scalarNode('class')
                    ->defaultValue('HoPeter1018\SequentialCounterFormatBundle\Entity\SequentialCounter')
                ->end()
                ->arrayNode('managers')
                    ->defaultValue(['default'])
                    ->prototype('scalar')->end()
                ->end()
                // ->arrayNode('mapping')
                //     ->addDefaultsIfNotSet()
                //     ->children()
                //         ->arrayNode('paths')
                //             ->prototype('scalar')->end()
                //         ->end()
                //     ->end()
                // ->end()
                ->arrayNode('rules')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('entity_class')->end()
                            ->scalarNode('property')->end()
                            ->scalarNode('format')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
