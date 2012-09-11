<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DropDatabaseWordpressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Drops database for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env        = Validators::validateEnv($input->getOption('env'));
        $yaml       = Yaml::parse(realpath(__DIR__.'/../../../config/skeleton.yml'));
        $deploy     = $yaml['deploy'][$env]['db'];
        $wordpress  = $yaml['wordpress'][$env]['db'];

        $command = sprintf('mysql --host=%s --user=%s --password=%s --execute=%s',
            escapeshellarg($wordpress['host']),
            escapeshellarg($deploy['user']),
            escapeshellarg($deploy['password']),
            escapeshellarg(sprintf('DROP DATABASE IF EXISTS %s', $wordpress['name']))
        );

        $output->writeln(sprintf('Running <info>%s</info>', $command));

        passthru($command, $error);

        if ($error) {
            $output->writeln("\t<error>FAILED</error>");
        } else {
            $output->writeln("\t<comment>SUCCESS</comment>");
        }
    }
}
