<?php namespace Factories;

use Illuminate\Filesystem\Filesystem;
use Configuration\ConfigReader;

class ConfigReaderFactory
{
    public static function make($configFile, Filesystem $fs = null)
    {
        return new ConfigReader($fs ?: new Filesystem, (empty($configFile) ? null : $configFile));
    }
}
