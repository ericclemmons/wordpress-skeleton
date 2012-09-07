<?php

namespace Skeleton\Generator;

use Phly\Mustache\Mustache;

class Generator
{
    private $source;

    private $destination;

    private $mustache;

    public function __construct($source, $destination)
    {
        $this->source       = $source;
        $this->destination  = $destination;
        $this->mustache     = new Mustache();

        $this->mustache->setTemplatePath($this->source);
    }

    public function generateIpAddress()
    {
        $blocks = array(
            array('10.0.0.0', '10.255.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.168.0.0', '192.168.255.255'),
        );

        $block  = $blocks[array_rand($blocks)];
        $range  = array_map('ip2long', $block);

        $long   = rand(current($range) + 1, end($range) - 1);
        $ip     = long2ip($long);

        return $ip;
    }

    /**
     * Generates a template to a destination with a given scope
     *
     * @param string Source template name
     * @param string Path to destination
     * @param array Template scope variables
     */
    public function generateSkeleton(array $context = array())
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->source, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $path => $file) {
            $template = str_replace(array($this->source.'/', '.mustache'), null, $path);
            $contents = $this->generateTemplate($template, $context);

            $target     = sprintf('%s/%s', $this->destination, $template);
            $success    = file_put_contents($target, $contents);

            if (!$success) {
                throw new \Exception('Unable to write to '.$target);
            }
        }
    }

    public function generateTemplate($template, array $context = array())
    {
        return $this->mustache->render($template, $context);
    }
}
