<?php namespace Delegates;

use Console\OptionReader;
use Console\GenerateCommand;
use Configuration\ConfigReaderInterface;
use Configuration\ConfigReader;
use Factories\GeneratorFactory;
use Illuminate\Filesystem\Filesystem;

/**
 * Class for delegation of application operation related
 * to executing a generation request and seeing that it is 
 * completed properly
 */
class GeneratorDelegate implements GeneratorDelegateInterface
{

    /**
     * Command that delegated the request
     * 
     * @var Console\GenerateCommand
     */
    protected $command;

    /**
     * Configuration of generation
     * 
     * @var Configuration\ConfigReader
     */
    protected $config;

    /**
     * Generator to perform the code generation
     * 
     * @var Generators\Generator
     */
    protected $generator;

    /**
     * String containing the requested generation action
     * 
     * @var string
     */
    protected $generation_request;

    /**
     * String containing the entity name the 
     * requested generation should use
     * 
     * @var string
     */
    protected $generate_for_entity;

    /**
     * Options passed in for generation
     * 
     * @var \Console\OptionReader
     */
    protected $optionReader;

    /**
     * Filesystem object for interacting
     * with the filesystem when needed
     * @var Filesystem
     */
    protected $filesystem;


    /**
     * @inheritDoc
     */
    public function __construct(
        GenerateCommand $cmd,
        ConfigReaderInterface $cfg,
        GeneratorFactory $genFactory,
        Filesystem $filesystem,
        array $command_args,
        OptionReader $optionReader
    ) {
        $this->command             = $cmd;
        $this->config              = $cfg;
        $this->generator           = @$genFactory->make($command_args['what'], $optionReader);
        $this->filesystem          = $filesystem;
        $this->generate_for_entity = $command_args['entity'];
        $this->generation_request  = $command_args['what'];
        $this->optionReader        = $optionReader;
    }


    /**
     * Function to run the delegated operations and return boolean
     * status of result.  If false the command should comment out
     * the reasons for the failure.
     * 
     * @return bool success / failure of operations
     */
    public function run()
    {
        //check if the loaded config is valid
        if (! $this->config->validateConfig()) {
            $this->command->comment(
                'Error',
                'The loaded configuration file is invalid',
                true
            );
            return false;
        }

        //get possible generations
        $possible_generations = $this->config->getAvailableGenerators(
            $this->config->getConfigType()
        );

        //see if passed in command is one that is available
        if (! in_array($this->generation_request, $possible_generations)) {
            $this->command->comment(
                'Error',
                "{$this->generation_request} is not a valid option",
                true
            );

            $this->command->comment(
                'Error Details',
                "Please choose from: ". implode(", ", $possible_generations),
                true
            );
            return false;
        }

        //should be good to generate, get the config values
        $settings  = $this->config->getConfigValue($this->generation_request);

        $tplFile   = $settings[ConfigReader::CONFIG_VAL_TEMPLATE];
        $template  = implode(DIRECTORY_SEPARATOR, [$this->config->getConfigDirectory(), $tplFile]);
        $directory = $settings[ConfigReader::CONFIG_VAL_DIRECTORY];
        $filename  = $settings[ConfigReader::CONFIG_VAL_FILENAME];

        //run generator
        $success = $this->generator->make(
            $this->generate_for_entity,
            $template,
            $directory,
            $filename,
            $this->optionReader
        );

        if ($success) {

            $this->command->comment(
                'Blacksmith',
                'Success, I generated the code for you in '. $this->generator->getTemplateDestination()
            );
            return true;

        } else {

            $this->command->comment(
                'Blacksmith',
                'An unknown error occured, nothing was generated',
                true
            );
            return false;
        }
    }
}
