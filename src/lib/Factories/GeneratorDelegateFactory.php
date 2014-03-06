<?php namespace Factories;

use Console\GenerateCommand;
use Delegates\GeneratorDelegate;
use Factories\GeneratorFactory;
use Factories\ConfigReaderFactory;

class GeneratorDelegateFactory
{
    const ARCH_HEXAGONAL = "hexagonal";


    public static function make(GenerateCommand $cmd, array $args, array $options)
    {
        $architecture = array_key_exists('architecture', $options) ? $options['architecture'] : static::ARCH_HEXAGONAL;

        switch ($architecture) {

            case static::ARCH_HEXAGONAL:
                $delegate = new GeneratorDelegate(
                    $cmd,
                    ConfigReaderFactory::make($args['config-file']),
                    GeneratorFactory::make($args['what']),
                    $args,
                    $options
                );
                break;

            default:
                throw new \InvalidArgumentException("[{$architecture}] is not a valid architecture option");
                break;

        }//end switch

        return $delegate;
    }
}
