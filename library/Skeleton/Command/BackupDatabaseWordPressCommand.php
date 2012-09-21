<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BackupDatabaseWordpressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Backups database locally to /backups directory for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env    = Validators::validateEnv($input->getOption('env'));
        $path   = sprintf('%s/backups/%s.sql.gz', $this->skeleton->getRoot(), date('Y-m-d.his'));

        $command = sprintf('mysqldump -u%s -p%s -h%s --opt --databases %s | sed %s | gzip --rsyncable > %s',
            escapeshellarg($this->skeleton->get('wordpress.%s.db.user', $env)),
            escapeshellarg($this->skeleton->get('wordpress.%s.db.password', $env)),
            escapeshellarg($this->skeleton->get('wordpress.%s.db.host', $env)),
            escapeshellarg($this->skeleton->get('wordpress.%s.db.name', $env)),
            escapeshellarg(sprintf('s/%s/skeleton_backup/g', $this->skeleton->get('wordpress.%s.db.name', $env))),
            $path
        );

        @mkdir(dirname($path), 0775, true);

        $output->writeln(sprintf('Running <info>%s</info>', $command));

        passthru($command, $error);

        if ($error) {
            $output->writeln("\t<error>FAILED</error>");

            return 1;
        } else {
            $output->writeln("\t<comment>SUCCESS</comment>");
        }
    }
}
