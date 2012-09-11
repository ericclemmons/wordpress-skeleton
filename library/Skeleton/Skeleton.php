<?php

namespace Skeleton;

use Symfony\Component\Yaml\Yaml;

class Skeleton
{
    private $config;

    public function __construct($configPath)
    {
        $this->config = Yaml::parse($configPath);
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

    public function has($property)
    {
        try {
            $this->get($property);

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
}
