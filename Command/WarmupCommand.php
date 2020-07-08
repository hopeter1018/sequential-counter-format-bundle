<?php

/*
 * <hokwaichi@gmail.com>
 */

declare(strict_types=1);

namespace HoPeter1018\SequentialCounterFormatBundle\Command;

use HoPeter1018\SequentialCounterFormatBundle\CacheWarm\MappingCacheWarmUp;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WarmupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
          ->setName('hopeter1018:scf:warm')
          ->setDescription('Sequential Counter Format Cache Warmup')
          ->setHelp('This command is to Sequential Counter Format Cache Warmup')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('[hopeter1018] Sequential Counter Format Cache Warmup');

        $warmup = $this->getContainer()->get(MappingCacheWarmUp::class);
        $warmup->warmUp('');

        $io->text('Done.');
    }
}
