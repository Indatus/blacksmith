<?php namespace Configuration;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Class for reading files and values from Blacksmith
 * configuration file
 */
class ConfigReader
{

    /**
     * Filesystem object for interacting
     * with files and directories
     * 
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Var to hold the parsed configuration
     * 
     * @var array
     */
    protected $config = null;

    /**
     * Constants for configuration keys that
     * may exist
     */
    const CONFIG_TYPE_HEXAGONAL        = 'hexagonal';
    const CONFIG_TYPE_KEY              = 'config_type';
    const CONFIG_VAL_TEMPLATE          = 'template';
    const CONFIG_VAL_DIRECTORY         = 'directory';
    const CONFIG_VAL_FILENAME          = 'filename';
    //-------[ key names ]--------------------------//
    const CONFIG_KEY_MODEL             = 'model';
    const CONFIG_KEY_CONTROLLER        = 'controller';
    const CONFIG_KEY_SEED              = 'seed';
    const CONFIG_KEY_MIGRATION_CREATE  = 'migration_create';
    const CONFIG_KEY_VIEW_CREATE       = 'view_create';
    const CONFIG_KEY_VIEW_UPDATE       = 'view_update';
    const CONFIG_KEY_VIEW_SHOW         = 'view_show';
    const CONFIG_KEY_VIEW_INDEX        = 'view_index';
    const CONFIG_KEY_FORM              = 'form';
    const CONFIG_KEY_TEST_UNIT         = 'test_unit';
    const CONFIG_KEY_TEST_FUNCTIONAL   = 'test_functional';
    const CONFIG_KEY_SERVICE_CREATOR   = 'service_creator';
    const CONFIG_KEY_SERVICE_UPDATER   = 'service_updater';
    const CONFIG_KEY_SERVICE_DESTROYER = 'service_destroyer';
    const CONFIG_KEY_VALIDATOR         = 'validator';

    /**
     * Types of configs that this 
     * reader will support
     * 
     * @var array
     */
    protected $config_types = [
        "hexagonal"
    ];

    /**
     * Config keys required for the hexagonal
     * config type
     * 
     * @var array
     */
    protected $hexagonal_config_keys = [
        "model",
        "controller",
        "seed",
        "migration_create",
        "view_create",
        "view_update",
        "view_show",
        "view_index",
        "form",
        "test_unit",
        "test_functional",
        "service_creator",
        "service_updater",
        "service_destroyer",
        "validator",
    ];


    /**
     * Constructor function to setup our class variables
     * and load / parse the passed in config or load the default
     * 
     * @param Filesystem $fs  
     * @param string     $path
     */
    public function __construct(Filesystem $fs, $path = null)
    {
        $this->filesystem = $fs;

        if (!is_null($path) && $this->filesystem->exists($path)) {

            $this->config = json_decode($this->filesystem->get($path), true);

        } else {
            $default = realpath(__DIR__.'/../Generators/templates/hexagonal/config.json');
            $this->config = json_decode($this->filesystem->get($path), true);
        }
    }


    /**
     * Function to validate the currently loaded config
     * 
     * @return bool
     */
    public function validateConfig()
    {
        if (! array_key_exists(static::CONFIG_TYPE_KEY, $this->config)) {
            return false;
        }

        $keys_var = $this->config[static::CONFIG_TYPE_KEY].'_config_keys';

        foreach ($this->{$keys_var} as $key) {
            if (! array_key_exists($key, $this->config)) {
                return false;
            }

            if (! array_key_exists(static::CONFIG_VAL_TEMPLATE, $this->config[$key])) {
                return false;
            }

            if (! array_key_exists(static::CONFIG_VAL_DIRECTORY, $this->config[$key])) {
                return false;
            }

            if (! array_key_exists(static::CONFIG_VAL_FILENAME, $this->config[$key])) {
                return false;
            }
        }

        return true;
    }


    /**
     * Function to return an array of possible
     * generations for the loaded config
     *
     * @param string $config_type
     * @return array
     */
    public function getAvailableGenerators($config_type)
    {
        if (! in_array($config_type, $this->config_types)) {
            return false;
        }

        $keys_var = $config_type.'_config_keys';

        return $this->{$keys_var};
    }


    /**
     * Function to return the loaded config type
     * 
     * @return string
     */
    public function getConfigType()
    {
        return $this->config[static::CONFIG_TYPE_KEY];
    }


    /**
     * Function to get a configuration 
     * key's values
     * 
     * @param  string $key valid config key
     * @return array       config value for given key
     */
    public function getConfigValue($key)
    {
        $keys_var = $this->config[static::CONFIG_TYPE_KEY].'_config_keys';

        if (! in_array($key, $this->{$keys_var})) {
            return false;
        }

        return $this->config[$key];
    }
}
