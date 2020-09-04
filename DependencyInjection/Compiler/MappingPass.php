<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\DependencyInjection\Compiler;

use HoPeter1018\SequentialCounterFormatBundle\CacheWarm\MappingCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        /** @var MappingCache */
        $mappingCacheDefinition = $container->getDefinition(MappingCache::class);

        foreach ($bundles as $bundleName => $bundleFqcn) {
            $path = "@{$bundleName}/Resources/config/sequential_counter_format.yaml";
            $mappingCacheDefinition->addMethodCall('addMappingPath', [$path]);
        }
    }
}
