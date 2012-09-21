<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InfoSkeletonCommand extends SkeletonCommand
{
    protected function configure()
    {
        $this->setDescription('Displays skeleton configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog     = $this->getDialogHelper();
        $wordpress  = $this->skeleton->get('wordpress');

        foreach ($wordpress as $env => $config) {
            $dialog->writeSection($output, sprintf('%s WordPress Settings', ucfirst($env)));

            $width      = 0;
            $properties = $this->getProperties(array(
                'admin' => $config['admin'],
                'db'    => $config['db'],
            ));

            foreach ($properties as $property => $value) {
                $width = (strlen($property) > $width) ? strlen($property) : $width;
            }

            foreach ($properties as $property => $value) {
                $output->writeln(sprintf('<info>%s</info>%s  : <comment>%s</comment>',
                    $property, str_repeat(' ', $width - strlen($property)),
                    is_null($value) ? '~' : $value
                ));
            }
        }

        $output->writeln('');
        $output->writeln('You view all the settings in <info>skeleton.yml</info>');
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog || !$dialog instanceof DialogHelper) {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }


    private function getProperties(array $config = array(), $prefix = null)
    {
        $properties = array();

        foreach ($config as $key => $value) {
            $property = ($prefix ? sprintf("%s.", $prefix) : null).$key;

            if (is_array($value)) {
                $properties += $this->getProperties($value, $property);
            } else {
                $properties[$property] = $value;
            }
        }

        return $properties;
    }
}
