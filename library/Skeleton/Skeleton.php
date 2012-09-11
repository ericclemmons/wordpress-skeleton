<?php

namespace Skeleton;

use Skeleton\Generator\Generator;
use Symfony\Component\Yaml\Yaml;

class Skeleton
{
    private $config;

    private $configPath;

    public function __construct($configPath)
    {
        $this->configPath   = $configPath;
        $this->generator    = new Generator(__DIR__.'/Resources/skeleton', $this->getRoot());

        $this->initConfig();
    }

    public function generate()
    {
        if (!$this->hasConfig()) {
            throw new \LogicException('Cannot generate skeleton without being configured first');
        }

        return $this->generator->generateSkeleton($this->getConfig());
    }

    public function get($property)
    {
        $paths  = explode('.', $property);
        $root   = $this->config;

        while ($path = array_shift($paths)) {
            if (isset($root[$path])) {
                $root = $root[$path];
            } else {
                throw new \InvalidArgumentException(sprintf('Property %s is not set', $property));
            }
        }

        return $root;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigPath()
    {
        return $this->configPath;
    }

    public function getGenerator()
    {
        return $this->generator;
    }

    public function getRoot()
    {
        return realpath(__DIR__.'/../../');
    }

    public function has($property)
    {
        try {
            $this->get($property);

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    public function hasConfig()
    {
        return is_array($this->config);
    }

    public function initConfig()
    {
        if (is_file($this->configPath)) {
            $this->config = Yaml::parse($this->configPath);
        }
    }

    public function writeConfig(array $config)
    {
        $yaml   = Yaml::dump($config, 8);
        $path   = $this->getConfigPath();
        $dir    = dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0775)) {
            throw new \Exception('Unable to create folder '.$dir);
        }

        $success = file_put_contents($path, $yaml);

        $this->initConfig();

        return $success;
    }
}
