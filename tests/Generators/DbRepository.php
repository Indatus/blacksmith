<?php namespace Generators;

use Console\OptionReader;
use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class DbRepositoryTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new DbRepository(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser,
            new OptionReader([])
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }
}
