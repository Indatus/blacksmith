<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;

class Controller extends Generator
{

    /**
     * Constructor to set member vars
     * 
     * @param Filesystem $filesystem
     * @param Mustache_Engine $mustache
     */
    public function __construct(Filesystem $filesystem, Mustache_Engine $mustache, FieldParser $fieldParser)
    {
        parent::__construct($filesystem, $mustache, $fieldParser);
    }
}
