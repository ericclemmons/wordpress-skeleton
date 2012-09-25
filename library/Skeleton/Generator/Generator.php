<?php

namespace Skeleton\Generator;

use Skeleton\Skeleton;

class Generator
{
    private $skeleton;

    private $source;

    private $destination;

    public function __construct(Skeleton $skeleton, $source, $destination)
    {
        $this->skeleton     = $skeleton;
        $this->source       = $source;
        $this->destination  = $destination;
    }

    /**
     * Generates a template to a destination with a given scope
     *
     * @param string Source template name
     * @param string Path to destination
     * @param array Template scope variables
     */
    public function generateSkeleton()
    {
        $files      = new \RecursiveDirectoryIterator($this->source, \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $iterator   = new \RecursiveIteratorIterator($files);
        $generated  = array();

        foreach ($iterator as $path => $file) {
            if ($file->getBasename() === '.git') {
                return false;
            }

            $template = str_replace(array($this->source.'/', '.mustache'), null, $path);
            $contents = file_get_contents($path);

            if ($file->getExtension() === 'mustache') {
                $contents = $this->parse($contents);
            }

            $target = $this->parse(sprintf('%s/%s', $this->destination, $template));

            if (is_dir($target)) {
                continue;
            }

            $parentDir = dirname($target);

            if (!is_dir($parentDir) && !mkdir($parentDir, 0775, true)) {
                throw new \Exception('Unable to create directory '.$parentDir);
            }

            if (!file_put_contents($target, $contents)) {
                throw new \Exception('Unable to write to '.$target);
            }

            $generated[] = $target;
        }

        return $generated;
    }

    public function parse($template)
    {
        $skeleton = $this->skeleton;

        $parsed = preg_replace_callback('/{{{?([\w.]+)}}}?/', function($matches) use ($skeleton) {
            return $skeleton->get($matches[1]);
        }, $template);

        return $parsed;
    }
}
