<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class CreateDatabaseWordpressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Creates database for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env        = Validators::validateEnv($input->getOption('env'));

        // Internal WordPress connection details
        $wpName     = $this->skeleton->get('wordpress.%s.db.name',      $env);
        $wpUser     = $this->skeleton->get('wordpress.%s.db.user',      $env);
        $wpPassword = $this->skeleton->get('wordpress.%s.db.password',  $env);
        $wpHost     = $this->skeleton->get('wordpress.%s.db.host',      $env);

        // Database admin user
        $dbUser     = $this->skeleton->get('deploy.%s.db.user',         $env);
        $dbPassword = $this->skeleton->get('deploy.%s.db.password',     $env);

        // External host name
        $webHost    = $this->skeleton->get('deploy.%s.web.host',        $env);

        // Grant access to WP database for WP user + password
        $commands   = array(
            sprintf('CREATE DATABASE IF NOT EXISTS %s', $wpName),
            sprintf('GRANT ALL ON %s.* TO %s@%s IDENTIFIED BY %s', $wpName, escapeshellarg($wpUser), escapeshellarg('%'),       escapeshellarg($wpPassword)),
            sprintf('GRANT ALL ON %s.* TO %s@%s IDENTIFIED BY %s', $wpName, escapeshellarg($wpUser), escapeshellarg($wpHost),   escapeshellarg($wpPassword)),
            sprintf('GRANT ALL ON %s.* TO %s@%s IDENTIFIED BY %s', $wpName, escapeshellarg($wpUser), escapeshellarg($webHost),  escapeshellarg($wpPassword)),
            'FLUSH PRIVILEGES',
        );

        // Use database admin for granting access
        foreach ($commands as $command) {
            $wrapper = sprintf('mysql --host=%s --user=%s --password=%s --execute=%s',
                escapeshellarg($wpHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPassword),
                escapeshellarg($command)
            );

            $output->write(sprintf('Running <info>%s</info>...', $wrapper));

            passthru($wrapper, $error);

            if ($error) {
                $output->writeln('<error>FAILED</error>');

                return 1;
            } else {
                $output->writeln('<comment>SUCCESS</comment>');
            }
        }
    }
}
