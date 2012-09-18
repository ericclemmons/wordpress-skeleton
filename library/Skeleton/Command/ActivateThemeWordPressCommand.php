<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateThemeWordpressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Activates WordPress theme for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for'),
                new InputOption('theme', '', InputOption::VALUE_REQUIRED, 'Theme name to activate'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env    = Validators::validateEnv($input->getOption('env'));
        $root   = realpath(__DIR__.'/../../../web');
        $theme  = $input->getOption('theme') ?: $this->skeleton->get('domain');

        require $root.'/wp-load.php';

        $output->write(sprintf('Activating theme <comment>%s</comment>...', $theme));

        switch_theme($theme);

        $output->writeln('<info>DONE</info>');
    }
}
