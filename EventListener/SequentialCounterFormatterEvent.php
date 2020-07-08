<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use HoPeter1018\SequentialCounterFormatBundle\Services\SequentialCounterFormatter;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class SequentialCounterFormatterEvent
  // implements EventSubscriber
{
    private $scf;
    private $propertyAccessor;

    public function __construct(SequentialCounterFormatter $scf, PropertyAccessorInterface $propertyAccessor)
    {
        $this->scf = $scf;
        $this->scf = $scf;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->gen($args);
    }

    protected function gen(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $this->scf->setEm($args->getObjectManager());
        $this->scf->checkAndSetFormattedCounter($entity);
    }
}
