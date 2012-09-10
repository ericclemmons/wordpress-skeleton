<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallWordpressCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Installs WordPress using wp-config.php')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webRoot = realpath(__DIR__.'/../../../web');

        define('WP_INSTALLING', true);

        /** Load WordPress Bootstrap */
        require_once $webRoot.'/wp-load.php';

        /** Load WordPress Administration Upgrade API */
        require_once $webRoot.'/wp-admin/includes/upgrade.php';

        /** Load wpdb */
        require_once $webRoot.'/wp-includes/wp-db.php';

        // require_once $webRoot.'/wp-config.php';

        if (is_blog_installed()) {
            $output->writeln('<error>Already installed.</error>');

            return 1;
        }

        $output->write('Installing...');

        $result = wp_install('WordPress Skeleton', DB_USER, null, true, '', DB_PASSWORD);

        if (is_wp_error($result)) {
            throw new \Exception($result);
        } else {
            $output->writeln('DONE.');

            $output->writeln(sprintf('Login as <info>%s</info> with the password <info>%s</info>', DB_USER, DB_PASSWORD));
        }
    }
}
