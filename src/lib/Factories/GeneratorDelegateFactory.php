<?php namespace Factories;

use Console\GenerateCommand;
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
     * @param  GenerateCommand $cmd     cli command that was run
     * @param  array           $args    cmmand arguments
     * @param  array           $options command options
     * @return Delegates\GeneratorDelegate
     */
    public function make(GenerateCommand $cmd, array $args, array $options)
    {
        $architecture = array_key_exists('architecture', $options) ? $options['architecture'] : static::ARCH_HEXAGONAL;

        switch ($architecture) {

            case static::ARCH_HEXAGONAL:
                $delegate = $this->makeHexagonalGeneratorDelegate($cmd, $args, $options);
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
     * @param  GenerateCommand $cmd     [description]
     * @param  array           $args    [description]
     * @param  array           $options [description]
     * @return [type]                   [description]
     */
    public function makeHexagonalGeneratorDelegate(GenerateCommand $cmd, array $args, array $options)
    {
        if (in_array($args['what'], static::$aggregate_generators)) {

            $delegate = new AggregateGeneratorDelegate(
                $cmd,
                $this->configFactory->make($args['config-file']),
                $this->generatorFactory,
                $this->filesystem,
                $args,
                $options
            );

        } else {

            $delegate = new GeneratorDelegate(
                $cmd,
                $this->configFactory->make($args['config-file']),
                $this->generatorFactory,
                $this->filesystem,
                $args,
                $options
            );
        }
        

        return $delegate;
    }
}
