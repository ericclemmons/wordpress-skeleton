<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeTestImportWordPressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Imports the Theme Unit Test into WordPress for specified environment')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for')
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $wp_importers;

        $env    = Validators::validateEnv($input->getOption('env'));
        $root   = $this->skeleton->getWebRoot();

        define('WP_LOAD_IMPORTERS', true);

        require $root.'/wp-load.php';
        require ABSPATH.'wp-admin/includes/admin.php';

        $error = activate_plugin('wordpress-importer/wordpress-importer.php');

        if (is_wp_error($error)) {
            throw new \Exception('WordPress Importer could not be activated.  Is it installed?');
        }

        do_action('admin_init');

        // 0 - Name, 1 = Description, 2 = Callable
        $importer = $wp_importers['wordpress'][2][0];

        if (empty($importer)) {
            throw new \Exception('WordPress Importer could not be loaded');
        }

        $url        = 'https://wpcom-themes.svn.automattic.com/demo/test-data.2011-01-17.xml';
        $name       = basename($url);
        $file       = sprintf('%s/%s', sys_get_temp_dir(), $name);

        $output->write(sprintf('Downloading <info>%s</info> to <info>%s</info>...', $url, $file));

        if ($fixture = file_get_contents($url)) {
            $output->writeln('<comment>DONE</comment>');
        } else {
            throw new \Exception('Unable to download '.$file);
        }

        if (!file_put_contents($file, $fixture)) {
            throw new \Excception('Unable to temporarily write fixture to '.$file);
        }

        $output->write('Importing...');

        $_POST['fetch_attachments'] = true;

        ob_start();
        @$importer->import($file);
        ob_end_clean();

        $output->writeln('<comment>DONE</comment>');
    }
}
