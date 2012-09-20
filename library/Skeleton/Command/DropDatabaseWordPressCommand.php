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

        // Internal WordPress connection details
        $wpName     = $this->skeleton->get('wordpress.%s.db.name',  $env);
        $wpHost     = $this->skeleton->get('wordpress.%s.db.host',  $env);

        // Database admin user
        $dbUser     = $this->skeleton->get('deploy.%s.db.user',     $env);
        $dbPassword = $this->skeleton->get('deploy.%s.db.password', $env);


        $command = sprintf('mysql --host=%s --user=%s --password=%s --execute=%s',
            escapeshellarg($wpHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPassword),
            escapeshellarg(sprintf('DROP DATABASE IF EXISTS %s', $wpName))
        );

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
