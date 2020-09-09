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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

class MappingCache
{
    /** @var string */
    protected $cacheFolder;

    /** @var bool */
    protected $debug;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var FileLocator */
    protected $fileLocator;

    /** @var Reader */
    protected $annotationReader;

    protected $mappingPathsByPath = [];

    public function __construct(string $cacheFolder, bool $debug, FileLocator $fileLocator, ManagerRegistry $managerRegistry, Reader $annotationReader)
    {
        $this->cacheFolder = $cacheFolder;
        $this->debug = $debug;
        $this->fileLocator = $fileLocator;
        $this->managerRegistry = $managerRegistry;
        $this->annotationReader = $annotationReader;
    }

    public function addMappingPath(string $path)
    {
        try {
            $path = $this->fileLocator->locate($path);
            $parsed = Yaml::parse((string) file_get_contents($path), Yaml::PARSE_CONSTANT);
            $this->mappingPathsByPath[$path] = $parsed;
        } catch (\InvalidArgumentException $ex) {
        }
    }

    public function rules($manager, string $entityFqcn)
    {
        $metadata = $manager->getMetadataFactory()->getMetadataFor($entityFqcn);

        $filename = $this->cacheFolder.'/hopeter1018/sequential-counter-format/rules'.'_'.str_replace('\\', '-', $entityFqcn).md5($entityFqcn);
        $cache = new ConfigCache($filename, $this->debug);
        $rules = [];
        if (!$cache->isFresh()) {
            $resources = [];
            $reflection = new ReflectionClass($entityFqcn);
            if (file_exists($reflection->getFileName())) {
                $resources[] = new FileResource($reflection->getFileName());
            }

            $rules = $this->getRulesOfEntityClass($manager, $metadata);

            $cache->write(serialize($rules), $resources);
        } else {
            $rules = unserialize(file_get_contents($filename));
        }

        if ($this->debug) {
            $this->getRulesOfEntityClassFromMappingPaths($rules, $entityFqcn);
        }

        return $rules;
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
                $start = 1;
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
                $start = 1;
                if (is_array($format)) {
                    $format = $propAnno->setting['format'];
                    $batchPrefix = $propAnno->setting['batchPrefix'];
                    $start = isset($propAnno->setting['start']) ? $propAnno->setting['start'] : 1;
                }
                $rules[$prefix.$property->name.'-'.$format] = [
                    'entity_class' => $entityFqcn,
                    'property' => $property->name,
                    'format' => $format,
                    'batch_prefix' => $batchPrefix,
                    'start' => $start,
                ];
                SequentialCounterFormatter::parseRule($rules[$prefix.$property->name.'-'.$format]);
            }
        }
        $this->getRulesOfEntityClassFromMappingPaths($rules, $entityFqcn);

        return $rules;
    }

    protected function getRulesOfEntityClassFromMappingPaths(&$rules, $entityFqcn)
    {
        $prefix = sprintf('%s-', $entityFqcn);
        foreach ($this->mappingPathsByPath as $path => $yaml) {
            if (isset($yaml[$entityFqcn]) and isset($yaml[$entityFqcn]['attributes'])) {
                foreach ($yaml[$entityFqcn]['attributes'] as $propertyName => $setting) {
                    $format = $setting['format'];
                    $batchPrefix = $setting['batchPrefix'];
                    $start = isset($setting['start']) ? $setting['start'] : 1;
                    $rules[$prefix.$propertyName.'-'.$format] = [
                      'entity_class' => $entityFqcn,
                      'property' => $propertyName,
                      'format' => $format,
                      'batch_prefix' => $batchPrefix,
                      'start' => $start,
                  ];
                    SequentialCounterFormatter::parseRule($rules[$prefix.$propertyName.'-'.$format]);
                }
            }
        }
    }
}
