<?php

namespace Skeleton\Command;

class Validators
{
    static public function validateDomain($domain)
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('Domain cannot be empty');
        }

        return strtolower($domain);
    }

    static public function validateIpAddress($ipAddress)
    {
        $validated  = filter_var($ipAddress, FILTER_VALIDATE_IP);
        $public     = filter_var($validated, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE);

        if (empty($ipAddress)) {
            throw new \InvalidArgumentException('IP Address cannot be empty');
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
