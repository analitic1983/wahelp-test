<?php

namespace common;

use InvalidArgumentException;

class Config
{
    /**
     * Possible autoload env file, and cache results
     *
     * @throws InvalidArgumentException
     */
    static function getEnv(string $name): string
    {
        $value = getenv($name);
        // Filter allowed values
        if (!preg_match('/^[0-9a-zA-Z\.]+$/', $value)) {
            throw new InvalidArgumentException("Config key '" . $name . "'. Invalid config value format: allowed only 0-9a-zA-Z. Used '" . $value . "'.");
        }
        return $value;
    }
}