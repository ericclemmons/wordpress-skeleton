<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigureSkeletonCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Configures skeleton for development & deployment')
            ->setDefinition(array(
                new InputOption('domain', '', InputOption::VALUE_REQUIRED, 'The destination domain for this theme'),
                new InputOption('ip-address', '', InputOption::VALUE_REQUIRED, 'The local development IP address (cannot b'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain     = Validators::validateDomain($input->getOption('domain'));
        $ipAddress  = Validators::validateIpAddress($input->getOption('ip-address'));

        $output->writeln('Configuring...');

        $config = array(
            'name'                  => $domain,
            'deploy'                => array(
                'develop'           => array(
                    'web'           => array(
                        'name'      => sprintf('vagrant.%s', $domain),
                        'host'      => $ipAddress,
                        'user'      => 'vagrant',
                        'password'  => 'vagrant',
                    ),
                    'db'            => array(
                        'user'      => 'root',
                        'password'  => 'vagrant',
                    ),
                ),
            ),
            'wordpress'             => array(
                'salts'             => file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/'),
                'develop'           => array(
                    'db'                => array(
                        'name'          => 'wordpress',
                        'host'          => 'localhost',
                        'user'          => 'vagrant',
                        'password'      => 'vagrant',
                    ),
                ),
            ),
        );

        $yaml   = Yaml::dump($config, 8);
        $path   = realpath(__DIR__.'/../../../config').'/skeleton.yml';

        if (file_put_contents($path, $yaml)) {
            $output->writeln(sprintf("\tGenerated <info>%s</info>", $path));
        } else {
            $output->writeln(sprintf('\t<error>Unable to write %s</error>', $path));

            return 1;
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        $dialog->writeSection($output, 'Welcome to the WordPress Skeleton Configurator!');

        /**
         * Domain
         */
        $domain = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('What is the destinaton domain (e.g. <comment>my-site.com</comment>)', $input->getOption('domain')),
            array('Skeleton\Command\Validators', 'validateDomain'),
            false,
            $input->getOption('domain')
        );

        $input->setOption('domain', $domain);

        /**
         * IP Address
         */
        $defaultIp = $input->getOption('ip-address') ?: $this->generator->generateIpAddress();
        $ipAddress = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('(Optional) Specify the internal Vagrant IP address', $defaultIp),
            array('Skeleton\Command\Validators', 'validateIpAddress'),
            false,
            $defaultIp
        );

        $input->setOption('ip-address', $ipAddress);

        $output->writeln('');
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog || !$dialog instanceof DialogHelper) {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}
