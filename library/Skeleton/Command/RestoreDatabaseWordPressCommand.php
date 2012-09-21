<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class RestoreDatabaseWordpressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Backups database locally to /backups directory for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for'),
                new InputOption('file', '', InputOption::VALUE_REQUIRED, 'Path to backup'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env    = Validators::validateEnv($input->getOption('env'));
        $path   = Validators::validatePath($input->getOption('file'));

        $command = sprintf('cat %s | sed %s | mysql -u%s -p%s -h%s',
            $path,
            escapeshellarg(sprintf('s/skeleton_backup/%s/g', $this->skeleton->get('wordpress.%s.db.name', $env))),
            escapeshellarg($this->skeleton->get('deploy.%s.db.user', $env)),
            escapeshellarg($this->skeleton->get('deploy.%s.db.password', $env)),
            escapeshellarg($this->skeleton->get('wordpress.%s.db.host', $env))
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
