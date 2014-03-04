<?php namespace Configuration;

use Illuminate\Filesystem\Filesystem;

/**
 * Interface that Blacksmith ConfigReaders must implement
 */
interface ConfigReaderInterface
{

    /**
     * Constructor function to setup our class variables
     * and load / parse the passed in config or load the default
     * 
     * @param Filesystem $fs  
     * @param string     $path
     */
    public function __construct(Filesystem $fs, $path = null);

    /**
     * Function to validate the currently loaded config
     * 
     * @return bool
     */
    public function validateConfig();

    /**
     * Function to return an array of possible
     * generations for the loaded config
     *
     * @param string $config_type
     * @return array
     */
    public function getAvailableGenerators($config_type);

    /**
     * Function to return the loaded config type
     * 
     * @return string
     */
    public function getConfigType();

    /**
     * Function to get a configuration 
     * key's values
     * 
     * @param  string $key valid config key
     * @return array       config value for given key
     */
    public function getConfigValue($key);
}
