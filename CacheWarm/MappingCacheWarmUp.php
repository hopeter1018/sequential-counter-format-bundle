<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\CacheWarm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class MappingCacheWarmUp implements CacheWarmerInterface
{
    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var MappingCache */
    protected $cache;

    /** @var array */
    protected $config;

    public function __construct($config, ManagerRegistry $managerRegistry, MappingCache $cache)
    {
        $this->managerRegistry = $managerRegistry;
        $this->cache = $cache;
        $this->config = $config;
    }

    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        $managerNames = array_keys($this->managerRegistry->getManagers());
        if (0 === count($this->config['managers'])) {
            $this->config['managers'] = $managerNames;
        }
        foreach ($this->config['managers'] as $managerName) {
            $manager = $this->managerRegistry->getManager($managerName);
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                $this->cache->rules($manager, $metadata->name);
            }
        }
    }
}
