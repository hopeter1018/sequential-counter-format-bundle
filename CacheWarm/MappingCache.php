<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\CacheWarm;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use HoPeter1018\SequentialCounterFormatBundle\Annotations\ClassRule;
use HoPeter1018\SequentialCounterFormatBundle\Annotations\PropertyRule;
use HoPeter1018\SequentialCounterFormatBundle\Services\SequentialCounterFormatter;
use ReflectionClass;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class MappingCache
{
    /** @var string */
    protected $cacheFolder;

    /** @var bool */
    protected $debug;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var Reader */
    protected $annotationReader;

    public function __construct(string $cacheFolder, bool $debug, ManagerRegistry $managerRegistry, Reader $annotationReader)
    {
        $this->cacheFolder = $cacheFolder;
        $this->debug = $debug;
        $this->managerRegistry = $managerRegistry;
        $this->annotationReader = $annotationReader;
    }

    public function rules($manager, string $entityFqcn)
    {
        $metadata = $manager->getMetadataFactory()->getMetadataFor($entityFqcn);

        $filename = $this->cacheFolder.'/hopeter1018/sequential-counter-format/rules'.'_'.str_replace('\\', '-', $entityFqcn).md5($entityFqcn);
        $cache = new ConfigCache($filename, $this->debug);
        if (!$cache->isFresh()) {
            $resources = [];
            $reflection = new ReflectionClass($entityFqcn);
            if (file_exists($reflection->getFileName())) {
                $resources[] = new FileResource($reflection->getFileName());
            }

            $rules = $this->getRulesOfEntityClass($manager, $metadata);

            $cache->write(serialize($rules), $resources);
        }

        return unserialize(file_get_contents($filename));
    }

    public function getRulesOfEntityClass($manager, $metadata)
    {
        $rules = [];
        $classAnno = $this->annotationReader->getClassAnnotation($metadata->reflClass, ClassRule::class);
        $entityFqcn = $metadata->reflClass->name;
        $prefix = sprintf('%s-', $entityFqcn);

        if (null !== $classAnno) {
            foreach ($classAnno->settings as $name => $setting) {
                $format = $setting;
                $batchPrefix = null;
                if (is_array($setting)) {
                    $format = $setting['format'];
                    $batchPrefix = $setting['batchPrefix'];
                    $start = isset($setting['start']) ? $setting['start'] : 1;
                }
                $rules[$prefix.$name.'-'.$format] = [
                    'entity_class' => $entityFqcn,
                    'property' => $name,
                    'format' => $format,
                    'batch_prefix' => $batchPrefix,
                    'start' => $start,
                ];
                SequentialCounterFormatter::parseRule($rules[$prefix.$name.'-'.$format]);
            }
        }
        foreach ($metadata->reflClass->getProperties() as $property) {
            $propAnno = $this->annotationReader->getPropertyAnnotation($property, PropertyRule::class);
            if (null !== $propAnno) {
                $format = $propAnno->setting;
                $batchPrefix = null;
                if (is_array($format)) {
                    $format = $propAnno->setting['format'];
                    $batchPrefix = $propAnno->setting['batchPrefix'];
                    $start = isset($setting['start']) ? $setting['start'] : 1;
                }
                $rules[$prefix.$name.'-'.$format] = [
                    'entity_class' => $entityFqcn,
                    'property' => $property->name,
                    'format' => $format,
                    'batch_prefix' => $batchPrefix,
                    'start' => $start,
                ];
                SequentialCounterFormatter::parseRule($rules[$prefix.$name.'-'.$format]);
            }
        }

        return $rules;
    }
}
