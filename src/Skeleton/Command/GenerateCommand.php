<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    private $generator;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $source         = Validators::validatePath(__DIR__.'/../Resources/skeleton');
        $destination    = Validators::validatePath(__DIR__.'/../../../');

        $this->generator = new Generator($source, $destination);
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates WordPress skeleton based on domain name')
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

        $this->generator->generateSkeleton(array(
            'domain'        => $domain,
            'ip_address'    => $ipAddress
        ));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        $dialog->writeSection($output, 'Welcome to the WordPress Skeleton Installer!');

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
