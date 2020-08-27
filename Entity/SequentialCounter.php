<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Peter Ho <hokwaichi@gmail.com>
 *
 * @ORM\Entity(repositoryClass="HoPeter1018\SequentialCounterFormatBundle\Repository\SequentialCounterRepository")
 * @UniqueEntity("batch")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class SequentialCounter
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Assert\NotNull
     */
    protected $counter;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull
     */
    protected $batch;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_fqcn", type="string", length=255)
     * @Assert\NotNull
     */
    protected $entityFqcn;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotNull
     */
    protected $start;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    public function getCounter(): ?int
    {
        return $this->counter;
    }

    public function setBatch(string $batch): self
    {
        $this->batch = $batch;

        return $this;
    }

    public function getBatch(): ?string
    {
        return $this->batch;
    }

    /**
     * Get the value of Entity Fqcn.
     *
     * @return string
     */
    public function getEntityFqcn()
    {
        return $this->entityFqcn;
    }

    /**
     * Set the value of Entity Fqcn.
     *
     * @param string $entityFqcn
     *
     * @return self
     */
    public function setEntityFqcn($entityFqcn)
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    /**
     * Get the value of Start.
     *
     * @return string
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set the value of Start.
     *
     * @param string $start
     *
     * @return self
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }
}
