<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class ServiceDestroyerTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new ServiceDestroyer(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser,
            m::mock('Console\OptionReader')
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }
}
