<?php namespace Generators;

use Console\OptionReader;
use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;

interface GeneratorInterface
{
    /**
     * @return void
     */
    public function __construct(Filesystem $filesystem, Mustache_Engine $mustache, FieldParser $fieldParser, OptionReader $optionReader);

    /**
     * @return boolean
     */
    public function make($entity, $sourceTemplate, $destinationDir, $fileName = null);
}
