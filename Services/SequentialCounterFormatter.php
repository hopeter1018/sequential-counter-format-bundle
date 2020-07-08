<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use HoPeter1018\SequentialCounterFormatBundle\CacheWarm\MappingCache;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Rule properties:
 *   - entity_class
 *   - property
 *   - format
 *   - batch_prefix
 * Rule format:
 *   - %04d
 *   - {YyMmWdwNHi}
 *   - [prop].
 *
 * @TODO use another db connection
 */
class SequentialCounterFormatter
{
    protected $config;
    protected $mappingCache;
    protected $ruleByName;
    protected $ruleNameByEntity;

    private $em;
    private $propertyAccessor;

    public function __construct($config, PropertyAccessorInterface $propertyAccessor, MappingCache $mappingCache)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->config = $config;
        $this->mappingCache = $mappingCache;

        if (isset($config['rules'])) {
            foreach ($config['rules'] as $configName => $entityConfig) {
                if (isset($entityConfig['entity_class']) and isset($entityConfig['property']) and isset($entityConfig['format'])) {
                    $entityConfig['entity_class'] = ltrim($entityConfig['entity_class'], '\\');
                    $this->ruleNameByEntity[$entityConfig['entity_class']] = $configName;
                    if (!isset($this->ruleByName[$configName])) {
                        $this->ruleByName[$configName] = [];
                    }

                    static::parseRule($entityConfig);
                    $this->ruleByName[$configName] = $entityConfig;
                }
            }
        }
    }

    public static function parseRule(&$entityConfig)
    {
        if (!isset($entityConfig['format'])) {
            $entityConfig['format'] = 'GEN{%05d}';
            $entityConfig['date_pattern'] = [];
        }

        preg_match_all('|{(.)}|', $entityConfig['format'], $matches);
        if (count($matches) > 1) {
            $entityConfig['batch_by_date'] = $matches[1];
            sort($entityConfig['batch_by_date']);
        } else {
            $entityConfig['batch_by_date'] = [];
        }

        preg_match_all('|[(.)]|', $entityConfig['format'], $matches);
        if (count($matches) > 1) {
            $entityConfig['batch_by_property'] = $matches[1];
            sort($entityConfig['batch_by_property']);
        } else {
            $entityConfig['batch_by_property'] = [];
        }
    }

    public function setEm(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function checkAndSetFormattedCounter($entity)
    {
        $rulesByAnno = $this->mappingCache->rules($this->em, get_class($entity));
        if (count($rulesByAnno) > 0) {
            foreach ($rulesByAnno as $ruleName => $rule) {
                $ruleBatch = $this->formatWithoutCounter($entity, $rule);
                $batchPrefix = $this->batchPrefix($entity, $rule);
                if (null === $this->propertyAccessor->getValue($entity, $rule['property'])) {
                    $counter = $this->em->getRepository($this->config['class'])->getNext($batchPrefix.$ruleBatch);
                    $this->propertyAccessor->setValue($entity, $rule['property'], $this->format($ruleBatch, $counter));
                }
            }
        }

        if (isset($this->config['class']) and isset($this->ruleNameByEntity[get_class($entity)])) {
            $ruleName = $this->ruleNameByEntity[get_class($entity)];
            $rule = $this->ruleByName[$ruleName];

            $ruleBatch = $this->formatWithoutCounter($entity, $rule);
            $batchPrefix = $this->batchPrefix($entity, $rule);
            if (null === $this->propertyAccessor->getValue($entity, $rule['property'])) {
                $counter = $this->em->getRepository($this->config['class'])->getNext($batchPrefix.$ruleBatch);
                $this->propertyAccessor->setValue($entity, $rule['property'], $this->format($ruleBatch, $counter));
            }
        }
    }

    protected function batchPrefix($entity, $rule)
    {
        if (isset($rule['batch_prefix']) and '' !== $rule['batch_prefix']) {
            $propertyAccessor = $this->propertyAccessor;

            return preg_replace_callback('|\\[([^\\\-]+)\\]|', function ($matches) use ($entity, $propertyAccessor) {
                return $propertyAccessor->getValue($entity, $matches[1]);
            }, $rule['batch_prefix']);
        } else {
            return '';
        }
    }

    protected function formatWithoutCounter($entity, $rule)
    {
        $propertyAccessor = $this->propertyAccessor;
        $dateParameters = 'Y-y-M-m-W-d-w-N-H-i';
        $keys = explode('-', '{'.str_replace('-', '}-{', $dateParameters).'}');
        $values = explode('-', date($dateParameters));
        $format = preg_replace_callback('|\\[([^\\\-]+)\\]|', function ($matches) use ($entity, $propertyAccessor) {
            return $propertyAccessor->getValue($entity, $matches[1]);
        }, $rule['format']);

        return str_replace($keys, $values, $format);
    }

    protected function format($ruleBatch, $counter)
    {
        // $propertyAccessor = $this->propertyAccessor;
        // $dateParameters = 'Y-y-M-m-W-d-w-N-H-i';
        // $keys = explode('-', '{'.str_replace('-', '}-{', $dateParameters).'}');
        // $values = explode('-', date($dateParameters));
        // $format = preg_replace_callback('|\\[([^\\\-]+)\\]|', function ($matches) use ($entity, $propertyAccessor) {
        //     return $propertyAccessor->getValue($entity, $matches[1]);
        // }, $rule['format']);

        return sprintf($ruleBatch, $counter);
    }
}
