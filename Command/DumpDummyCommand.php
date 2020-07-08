<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use HoPeter1018\SequentialCounterFormatBundle\CacheWarm\MappingCache;
use HoPeter1018\SequentialCounterFormatBundle\Services\SequentialCounterFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DumpDummyCommand extends Command
{
    /** @var ManagerRegistry */
    protected $managerRegistry;
    /** @var SequentialCounterFormatter */
    protected $scf;
    /** @var MappingCache */
    protected $mappingCache;

    public function __construct(ManagerRegistry $managerRegistry, MappingCache $mappingCache, SequentialCounterFormatter $scf)
    {
        $this->managerRegistry = $managerRegistry;
        $this->scf = $scf;
        $this->mappingCache = $mappingCache;
        parent::__construct();
    }

    protected function configure()
    {
        $this
          ->setName('hopeter1018:scf:dump-dummy')
          ->setDescription('Sequential Counter Format Dump Dummy')
          ->setHelp('This command is to Sequential Counter Format Dump Dummy')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('[hopeter1018] Sequential Counter Format Dump Dummy');

        // $cache = $this->getContainer()->get(MappingCache::class);
        // $sequentialCounterFormatter = $this->getContainer()->get(SequentialCounterFormatter::class);
        $manager = $this->managerRegistry->getManager('default');
        foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
            $rules = $this->mappingCache->rules($manager, $metadata->name);
            if (count($rules) > 0) {
                $entity = $metadata->newInstance();
                $this->scf->setEm($manager);
                $this->scf->checkAndSetFormattedCounter($entity);
                dump($entity);
            }
        }

        $io->text('Done.');
    }
}
