<?php

namespace Skeleton;

use Skeleton\Command\Validators;
use Skeleton\Generator\Generator;
use Symfony\Component\Yaml\Yaml;

class Skeleton
{
    private $config;

    private $configPath;

    public function __construct($configPath)
    {
        $this->configPath   = $configPath;
        $this->generator    = new Generator($this, __DIR__.'/Resources/skeleton', $this->getRoot());

        $this->initConfig();
    }

    public function generate()
    {
        if (!$this->hasConfig()) {
            throw new \LogicException('Cannot generate skeleton without being configured first');
        }

        return $this->generator->generateSkeleton();
    }

    public function get($property, $args = null)
    {
        if ($args) {
            $property = call_user_func_array('sprintf', func_get_args());
        }

        $paths  = explode('.', $property);
        $root   = $this->config;

        while ($path = array_shift($paths)) {
            if (isset($root[$path])) {
                $root = $root[$path];
            } else {
                return null;
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

    public function getWebRoot()
    {
        return $this->getRoot().'/web';
    }

    public function has($property)
    {
        return !is_null($this->get($property));
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

        // Ensure salts have enough spaces to prevent YAML parse errors
        if ($this->has('wordpress')) {
            foreach ($this->get('wordpress') as $env => $settings) {
                $property   = 'wordpress.'.$env.'.salts';
                $salts      = Validators::validateSalts($this->get($property));

                $this->set($property, $salts);
            }
        }

        return $this->getConfig();
    }

    public function set($property, $value)
    {
        $paths  = explode('.', $property);
        $root   = &$this->config;

        foreach ($paths as $path) {
            $root = &$root[$path];
        }

        $root = $value;

        return $this;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
