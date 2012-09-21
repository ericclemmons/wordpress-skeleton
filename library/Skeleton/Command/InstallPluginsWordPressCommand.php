<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Skeleton\WordPress\Upgrader\UpgraderSkin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallPluginsWordPressCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Installs WordPress plugins based on config')
            ->setDefinition(array(
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'Environment to use settings for'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env    = Validators::validateEnv($input->getOption('env'));
        $root   = realpath(__DIR__.'/../../../web');
        $plugins    = $this->skeleton->get(sprintf('wordpress.%s.plugins', $env));

        require $root.'/wp-load.php';
        require ABSPATH.'wp-admin/includes/admin.php';
        require ABSPATH.'wp-admin/includes/plugin-install.php';

        foreach ($plugins as $slug => $version) {
            $plugin = plugins_api('plugin_information', array('slug' => $slug));

            if (is_wp_error($plugin)) {
                throw new \Exception('Could not get plugin information for '.$slug);
            }

            if ($version) {
                list($prefix) = explode($slug, $plugin->download_link);

                $link       = sprintf('%s%s.%s.zip', $prefix, $slug, $version);
                $response   = wp_remote_head($link);

                if (!isset($response['response']['code']) || $response['response']['code'] != 200) {
                    throw new \Exception('Unable to verify '.$link);
                }

                $plugin->download_link  = $link;
                $plugin->version        = $version;
            }

            require ABSPATH.'wp-admin/includes/class-wp-upgrader.php';

            $status     = install_plugin_install_status($plugin);
            $upgrader   = new \Plugin_Upgrader(new UpgraderSkin($output));
            $current    = current(get_plugins("/$slug"));

            switch ($status['status']) {
                case 'install':
                    $output->write(sprintf('Installing <info>%s</info> v<comment>%s</comment>', $plugin->name, $plugin->version));
                    $upgrader->install($plugin->download_link);

                    break;

                case 'update_available':
                    if ($plugin->version == $current['Version']) {
                        $output->writeln(sprintf('<info>%s</info> v<comment>%s</comment> is already installed!', $plugin->name, $plugin->version));
                    } else {
                        $output->write(sprintf('Upgrading <info>%s</info> from <comment>%s</comment> to <comment>%s</comment>', $plugin->name, $current['Version'], $plugin->version));

                        $file = sprintf('%s/%s', $slug, key(get_plugins("/$slug")));
                        $upgrader->upgrade($file);
                    }

                    break;

                case 'latest_installed':
                    $output->writeln(sprintf('<info>%s</info> v<comment>%s</comment> is already installed!', $plugin->name, $current['Version']));

                    break;

                case 'newer_installed':
                    $output->writeln(sprintf('<info>%s</info> v<comment>%s</comment> is installed & newer than <comment>%s</comment>', $plugin->name, $current['Version'], $plugin->version));

                    break;
            }
        }

        if ($plugins) {
            $output->writeln(sprintf('<info>Activate plugins in the WordPress Admin</info>', $plugin->name));
        }
    }
}
