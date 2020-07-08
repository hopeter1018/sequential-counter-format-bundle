<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class PropertyRule
{
    public $setting = null;
}
