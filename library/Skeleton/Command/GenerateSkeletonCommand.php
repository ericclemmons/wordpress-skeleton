<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSkeletonCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Generates WordPress skeleton based on config')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->skeleton->hasConfig()) {
            $output->writeln('Generating...');

            $generated = $this->skeleton->generate();

            foreach ($generated as $file) {
                $output->writeln(sprintf("\tGenerated <info>%s</info>", $file));
            }
        } else {
            throw new \Exception(sprintf('Config not found.  Did you run `configure`?', $path));
        }

        $output->writeln('');
    }
}
