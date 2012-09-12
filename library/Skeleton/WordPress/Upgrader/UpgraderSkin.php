<?php

namespace Skeleton\WordPress\Upgrader;

use Symfony\Component\Console\Output\OutputInterface;

class UpgraderSkin extends \WP_Upgrader_Skin
{
    private $output;

    public function __construct(OutputInterface $output)
    {
        parent::__construct();

        $this->output = $output;
    }

    public function bulk_header()
    {}

    public function bulk_footer()
    {}

    public function feedback($message)
    {
        $this->output->write('.');
    }

    public function footer()
    {
        $this->output->writeln('');
    }

    public function header()
    {}
}
