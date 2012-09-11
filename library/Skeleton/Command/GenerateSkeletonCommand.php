<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Skeleton\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class GenerateSkeletonCommand extends SkeletonCommand
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
            ->setDescription('Generates WordPress skeleton based on config')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = realpath(__DIR__.'/../../../config').'/skeleton.yml';

        if (file_exists($path)) {
            $config = Yaml::parse($path);
        } else {
            throw new \Exception(sprintf('Could not load %s.  Did you run `skeleton:configure`?', $path));
        }

        $output->writeln('Generating...');

        $generated  = $this->generator->generateSkeleton($config);

        foreach ($generated as $file) {
            $output->writeln(sprintf("\tGenerated <info>%s</info>", $file));
        }
    }
}
