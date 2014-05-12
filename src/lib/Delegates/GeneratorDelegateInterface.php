<?php namespace Delegates;

use Console\OptionReader;
use Console\GenerateCommand;
use Configuration\ConfigReaderInterface;
use Factories\GeneratorFactory;
use Illuminate\Filesystem\Filesystem;

interface GeneratorDelegateInterface
{

    /**
     * Constructor to setup up our class variables
     *
     * @param GenerateCommand           $cmd          executed command
     * @param ConfigReaderInterface     $cfg          reader of the config file
     * @param array                     $command_args command arguments
     * @param OptionReader              $optionReader command options
     * @return void
     */
    public function __construct(
        GenerateCommand $cmd,
        ConfigReaderInterface $cfg,
        GeneratorFactory $gen,
        Filesystem $filesystem,
        array $command_args,
        OptionReader $optionReader
    );

    /**
     * @return boolean
     */
    public function run();
}
