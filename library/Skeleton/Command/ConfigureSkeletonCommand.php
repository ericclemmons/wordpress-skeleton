<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigureSkeletonCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this
            ->setDescription('Configures skeleton for development & deployment')
            ->setDefinition(array(
                new InputOption('domain', '', InputOption::VALUE_REQUIRED, 'The destination domain for this theme'),
                new InputOption('ip', '', InputOption::VALUE_REQUIRED, 'The local development IP address (cannot b'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = Validators::validateDomain($input->getOption('domain'));
        $ip     = Validators::validateIp($input->getOption('ip'));

        $output->writeln('Configuring...');

        $config = array(
            'name'                  => $domain,
            'deploy'                => array(
                'develop'           => array(
                    'web'           => array(
                        'host'      => sprintf('develop.%s', $domain),
                        'ip'        => $ip,
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
                    'db'            => array(
                        'name'      => 'wordpress',
                        'host'      => 'localhost',
                        'user'      => 'vagrant',
                        'password'  => 'vagrant',
                    ),
                    'plugins'       => array(
                        'wordpress-importer'    => null,
                    )
                ),
            ),
        );

        $path = $this->skeleton->getConfigPath();

        if ($this->skeleton->writeConfig($config)) {
            $output->writeln(sprintf("\tGenerated <info>%s</info>", $path));
        } else {
            throw new \Exception('Unable to write '.$path);
        }

        $next = $this->getApplication()->find('skeleton:generate');

        $next->run(new ArrayInput(array('command' => 'skeleton:generate')), $output);
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
        $defaultIp  = $input->getOption('ip') ?: $this->skeleton->getGenerator()->generateIp();
        $ip         = $dialog->askAndValidate(
            $output,
            $dialog->getQuestion('(Optional) Specify the internal Vagrant IP address', $defaultIp),
            array('Skeleton\Command\Validators', 'validateIp'),
            false,
            $defaultIp
        );

        $input->setOption('ip', $ip);

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
