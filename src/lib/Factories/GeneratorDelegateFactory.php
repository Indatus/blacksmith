<?php namespace Factories;

use Console\GenerateCommand;
use Console\OptionReader;
use Delegates\GeneratorDelegate;
use Delegates\AggregateGeneratorDelegate;
use Factories\GeneratorFactory;
use Factories\ConfigReaderFactory;
use Illuminate\Filesystem\Filesystem;

class GeneratorDelegateFactory
{
    const ARCH_HEXAGONAL = "hexagonal";

    /**
     * Array of generators that are aggregates of other generators
     * 
     * @var array
     */
    public static $aggregate_generators = ['resource', 'scaffold'];

    protected $configFactory;

    protected $generatorFactory;

    protected $filesystem;


    public function __construct(ConfigReaderFactory $configFactory, GeneratorFactory $generatorFactory, Filesystem $filesystem)
    {
        $this->configFactory = $configFactory;
        $this->generatorFactory = $generatorFactory;
        $this->filesystem = $filesystem;
    }


    /**
     * Primary method to make a generator delegate, finds
     * the architecture required and calls a subsequent
     * method to handle the details.
     * 
     * @param  GenerateCommand          $cmd            cli command that was run
     * @param  array                    $args           cmmand arguments
     * @param  \Console\OptionReader    $optionReader   command options
     * @return Delegates\GeneratorDelegate
     */
    public function make(GenerateCommand $cmd, array $args, OptionReader $optionReader)
    {
        $architecture = $optionReader->getArchitecture();

        switch ($architecture) {

            case static::ARCH_HEXAGONAL:
                $delegate = $this->makeHexagonalGeneratorDelegate($cmd, $args, $optionReader);
                break;

            default:
                throw new \InvalidArgumentException("[{$architecture}] is not a valid architecture option");
                break;

        }//end switch

        return $delegate;
    }


    /**
     * Function to handle the generation of hexagonal 
     * architecture generator delegates
     * 
     * @param  GenerateCommand          $cmd             [description]
     * @param  array                    $args            [description]
     * @param  \Console\OptionReader    $optionReader    [description]
     * @return [type]                           [description]
     */
    public function makeHexagonalGeneratorDelegate(GenerateCommand $cmd, array $args, OptionReader $optionReader)
    {
        if (in_array($args['what'], static::$aggregate_generators)) {

            $delegate = new AggregateGeneratorDelegate(
                $cmd,
                $this->configFactory->make($args['config-file']),
                $this->generatorFactory,
                $this->filesystem,
                $args,
                $optionReader
            );

        } else {

            $delegate = new GeneratorDelegate(
                $cmd,
                $this->configFactory->make($args['config-file']),
                $this->generatorFactory,
                $this->filesystem,
                $args,
                $optionReader
            );
        }
        

        return $delegate;
    }
}
