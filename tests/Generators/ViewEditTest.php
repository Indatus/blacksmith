<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;

class ViewEditTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new ViewEdit(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }
}
