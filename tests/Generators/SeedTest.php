<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class SeedTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new Seed(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }


    public function testMakeShould()
    {
        $fs = m::mock('Illuminate\Filesystem\Filesystem')->shouldIgnoreMissing();
        $me = m::mock('Mustache_Engine')->shouldDeferMissing();
        $fp = m::mock('Parsers\FieldParser');

        $entity         = 'order';
        $sourceTemplate = '/foo/bar/template.txt';
        $destinationDir = '/some/dir';
        $fileName       = 'SomeFileName.php';
        $fieldData      = null;

        $instance = m::mock(
            'Generators\Seed[updateDatabaseSeederRunMethod,getFileName]',
            [$fs, $me, $fp]
        );
        $instance->shouldReceive('getFileName')->times(3)->andReturn($fileName);
        $instance
            ->shouldReceive('updateDatabaseSeederRunMethod')
            ->once()
            ->with(basename($fileName, ".php"), m::any());

        //test pass
        $result = $instance->make(
            $entity,
            $sourceTemplate,
            $destinationDir,
            $fileName,
            $fieldData
        );
        $this->assertTrue($result);

        $fs->shouldReceive('exists')
            ->with(implode(DIRECTORY_SEPARATOR, [$destinationDir, $fileName]))
            ->andReturn(true);

        //test fail
        $result = $instance->make(
            $entity,
            $sourceTemplate,
            $destinationDir,
            $fileName,
            $fieldData
        );
        $this->assertFalse($result);
    }



    public function testUpdateDatabaseSeederRunMethod()
    {
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $me = m::mock('Mustache_Engine');
        $fp = m::mock('Parsers\FieldParser');
        $dir = '/some/path/name';
        $path = implode(DIRECTORY_SEPARATOR, [$dir, 'app', 'database', 'seeds', 'DatabaseSeeder.php']);
        $className = "OrderSeeder";

        //test file not found
        $fs->shouldReceive('exists')->once()->with($path)->andReturn(false);
        $instance = new Seed($fs, $me, $fp);
        $this->assertFalse($instance->updateDatabaseSeederRunMethod($className, $dir));

        //test file found and class already exists
        $fs->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $content = "blah blah \$this->call('{$className}'); blah blah";
        $fs->shouldReceive('get')->once()->with($path)->andReturn($content);
        $instance = new Seed($fs, $me, $fp);
        $this->assertFalse($instance->updateDatabaseSeederRunMethod($className, $dir));

        $fs->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $content = "blah blah blah blah";
        $fs->shouldReceive('get')->once()->with($path)->andReturn($content);
        $fs->shouldReceive('put')->once()->with($path, $content)->andReturn(true);
        $instance = new Seed($fs, $me, $fp);
        $this->assertTrue($instance->updateDatabaseSeederRunMethod($className, $dir));
    }
}
