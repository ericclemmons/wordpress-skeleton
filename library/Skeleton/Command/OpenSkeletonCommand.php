<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OpenSkeletonCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Opens the skeleton in your default browser (Default: <command>local</command>')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for', 'local')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env    = Validators::validateEnv($input->getOption('env'));
        $host   = $this->skeleton->get(sprintf('deploy.%s.web.host', $env));
        $url    = sprintf('http://%s/', $host);

        $output->writeln(sprintf('Opening <info>%s</info>...', $url));

        passthru("open $url");
    }
}
