<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;

class RepositoryInterfaceTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new RepositoryInterface(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }
}
