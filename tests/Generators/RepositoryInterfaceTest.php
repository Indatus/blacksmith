<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class RepositoryInterfaceTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new RepositoryInterface(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser,
            m::mock('Console\OptionReader')
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }
}
