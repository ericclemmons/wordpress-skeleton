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

    public function generateIp()
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
        $files      = new \RecursiveDirectoryIterator($this->source, \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $filtered   = new \RecursiveCallbackFilterIterator($files, function($file, $path, $iterator) {
            if ($file->getBasename() === '.git') {
                return false;
            }

            return true;
        });

        $generated = array();

        foreach (new \RecursiveIteratorIterator($filtered) as $path => $file) {
            $template = str_replace(array($this->source.'/', '.mustache'), null, $path);

            if ($file->getExtension() === 'mustache') {
                $contents = $this->generateTemplate($template, $context);
            } else {
                $contents = file_get_contents($path);
            }

            $target     = sprintf('%s/%s', $this->destination, $template);
            $parentDir  = dirname($target);

            if (!is_dir($parentDir) && !mkdir($parentDir, 0775)) {
                throw new \Exception('Unable to create directory '.$parentDir);
            }

            if (!file_put_contents($target, $contents)) {
                throw new \Exception('Unable to write to '.$target);
            }

            $generated[] = $target;
        }

        return $generated;
    }

    public function generateTemplate($template, array $context = array())
    {
        return $this->mustache->render($template, $context);
    }
}
