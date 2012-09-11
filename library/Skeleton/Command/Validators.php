<?php

namespace Skeleton\Command;

use Symfony\Component\Yaml\Yaml;

class Validators
{
    static public function validateDomain($domain)
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('Domain must be defined');
        }

        return strtolower($domain);
    }

    static public function validateEnv($env)
    {
        if (empty($env)) {
            throw new \InvalidArgumentException('Env must be defined');
        }

        $path = realpath(__DIR__.'/../../../config/skeleton.yml');

        if (!$path) {
            throw new \Exception('Could not find skeleton.yml');
        }

        $yaml = Yaml::parse($path);

        if (!isset($yaml['deploy'][$env])) {
            throw new \Exception(sprintf('No configuration defined for deploy.%s in skeleton.yml', $env));
        }

        return $env;
    }

    static public function validateIpAddress($ipAddress)
    {
        $validated  = filter_var($ipAddress, FILTER_VALIDATE_IP);
        $public     = filter_var($validated, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE);

        if (empty($ipAddress)) {
            throw new \InvalidArgumentException('IP Address must be defined');
        } elseif (!$validated) {
            throw new \InvalidArgumentException('IP Address is not a valid format');
        } elseif ($public) {
            throw new \InvalidArgumentException('IP Address cannot be public');
        }

        return $validated;
    }

    static public function validatePath($path)
    {
        $path = realpath($path);

        if (!$path) {
            throw new \InvalidArgumentException('Path does not exist');
        }

        return $path;
    }
}
