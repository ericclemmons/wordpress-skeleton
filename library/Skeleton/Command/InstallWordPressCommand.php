<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallWordpressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Installs WordPress for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = Validators::validateEnv($input->getOption('env'));

        $_SERVER['DOCUMENT_ROOT']   = getcwd();
        $_SERVER['SERVER_PROTOCOL'] = 'http';
        $_SERVER['HTTP_HOST']       = $this->skeleton->get('deploy.%s.web.host', $env);


        define('WP_ROOT', $this->skeleton->getWebRoot().'/');
        define('WP_INSTALLING', true);

        require WP_ROOT.'wp-load.php';
        require WP_ROOT.'wp-admin/includes/admin.php';
        require WP_ROOT.'wp-admin/includes/upgrade.php';

        if (is_blog_installed()) {
            $output->writeln('<error>Already installed.</error>');

            return 1;
        }

        $output->write('Installing...');

        $result = wp_install(
            $this->skeleton->get('name'),
            $this->skeleton->get('wordpress.%s.admin.user', $env),
            $this->skeleton->get('wordpress.%s.admin.email', $env),
            true,   // Public
            '',     // Deprecated
            $this->skeleton->get('wordpress.%s.admin.password', $env)
        );

        if (is_wp_error($result)) {
            throw new \Exception($result);
        }

        update_option('db_version', $wp_db_version);
        update_option('db_upgraded', true);

        $output->writeln('<info>DONE</info>');

        $output->writeln(sprintf("\nLogin as <info>%s</info> with the password <info>%s</info>",
            $this->skeleton->get('wordpress.%s.admin.user', $env),
            $this->skeleton->get('wordpress.%s.admin.password', $env)
        ));
    }
}
