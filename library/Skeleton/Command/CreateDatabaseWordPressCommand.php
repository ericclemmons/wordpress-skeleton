<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class CreateDatabaseWordpressCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Creates database specified in wp-config.php')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env        = Validators::validateEnv($input->getOption('env'));
        $yaml       = Yaml::parse(realpath(__DIR__.'/../../../config/skeleton.yml'));
        $deploy     = $yaml['deploy'][$env];
        $wordpress  = $yaml['wordpress'][$env];

        $commands   = array(
            sprintf('CREATE DATABASE IF NOT EXISTS %s', $wordpress['db']['name']),
            sprintf('GRANT ALL ON %s.* TO %s@%s IDENTIFIED BY %s', $wordpress['db']['name'], escapeshellarg($wordpress['db']['user']), escapeshellarg('%'), escapeshellarg($wordpress['db']['password'])),
            sprintf('GRANT ALL ON %s.* TO %s@%s IDENTIFIED BY %s', $wordpress['db']['name'], escapeshellarg($wordpress['db']['user']), escapeshellarg($deploy['web']['name']), escapeshellarg($wordpress['db']['password'])),
            sprintf('GRANT ALL ON %s.* TO %s@%s IDENTIFIED BY %s', $wordpress['db']['name'], escapeshellarg($wordpress['db']['user']), escapeshellarg($wordpress['db']['host']), escapeshellarg($wordpress['db']['password'])),
            'FLUSH PRIVILEGES',
        );

        foreach ($commands as $command) {
            $wrapper = sprintf('mysql --host=%s --user=%s --password=%s --execute=%s',
                escapeshellarg($wordpress['db']['host']),
                escapeshellarg($deploy['db']['user']),
                escapeshellarg($deploy['db']['password']),
                escapeshellarg($command)
            );

            $output->write(sprintf('Running <info>%s</info>...', $command));

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
