<?php namespace Factories;

use Console\OptionReader;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;
use Generators\GeneratorInterface;

class GeneratorFactory
{
    /**
     * Function to create a new generator based
     * on the given input
     * 
     * @param  string           $what the desired generator
     * @param  ReflectionClass  $reflectionClass used for finding out info about class to gen
     * @return Generators\GeneratorInterface
     */
    public function make($what, OptionReader $optionReader, $reflectionClass = null)
    {
        //use naming convention to convert the input name
        //into a fully quantified class name
        $klass = Str::studly($what);
        $fqcn  = "Generators\\{$klass}";

        try {
            $refl = $reflectionClass ?: new \ReflectionClass($fqcn);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException("Unsupported input [{$what}] to load [{$fqcn}]");
        }

        //make sure the generator implements the interface properly
        $interface = 'Generators\GeneratorInterface';

        if (! $refl->implementsInterface($interface)) {
            throw new \InvalidArgumentException("Unsupported interface [{$what}] must implement [{$interface}]");
        }
        
        $instance = $refl->newInstanceArgs([new Filesystem, new Mustache_Engine, new FieldParser, $optionReader]);

        return $instance;
    }
}
