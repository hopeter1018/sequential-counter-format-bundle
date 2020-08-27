<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Annotations;

/**
 * @Annotation
 * @Target("ALL")
 */
class Rule
{
    /**
     * @var string
     * @Required
     */
    public $format = '';

    /**
     * @var string
     */
    public $batchPrefix = '';

    /**
     * @var int|null
     */
    public $start = null;
}
