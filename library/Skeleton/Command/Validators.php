<?php

namespace Skeleton\Command;

use Symfony\Component\Yaml\Yaml;

class Validators
{
    static public function validateHost($host)
    {
        if (empty($host)) {
            throw new \InvalidArgumentException('Host must be defined');
        }

        $host   = strtolower(trim($host));
        $parts  = explode('.', $host);

        switch (count($parts)) {
            case 2:
            case 3:
                return $host;
            default:
                throw new \InvalidArgumentException('Host must follow the format: [subdomain.]domain.tld');
        }
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

        putenv('WP_ENV='.$env);

        return $env;
    }

    static public function validateIp($ip)
    {
        $validated  = filter_var($ip, FILTER_VALIDATE_IP);

        if (empty($ip)) {
            throw new \InvalidArgumentException('IP Address must be defined');
        } elseif (!$validated) {
            throw new \InvalidArgumentException('IP Address is not a valid format');
        }

        return $validated;
    }

    static public function validateName($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name must be defined');
        }

        return $name;
    }

    static public function validatePath($path)
    {
        $path = realpath($path);

        if (!$path) {
            throw new \InvalidArgumentException('Path does not exist');
        }

        return $path;
    }

    static public function validateRepository($repo)
    {
        $extension = pathinfo($repo, PATHINFO_EXTENSION);

        if ($extension !== 'git') {
            throw new \InvalidArgumentException('Repository must have a .git extension');
        }

        return $repo;
    }

    static public function validateSalts($salts)
    {
        return preg_replace("/\n(\w)/", "\n".str_repeat(' ', 12).'$1', trim($salts));
    }
}
