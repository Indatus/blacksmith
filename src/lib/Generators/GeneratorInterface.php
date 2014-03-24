<?php namespace Generators;

use Console\OptionReader;
use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;

interface GeneratorInterface
{
    public function __construct(Filesystem $filesystem, Mustache_Engine $mustache, FieldParser $fieldParser, OptionReader $optionReader);

    public function make($entity, $sourceTemplate, $destinationDir, $fileName = null);
}
