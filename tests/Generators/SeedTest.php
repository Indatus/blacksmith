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
            new FieldParser,
            m::mock('Console\OptionReader')
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }


    public function testMakeShould()
    {
        $fs = m::mock('Illuminate\Filesystem\Filesystem')->shouldIgnoreMissing();
        $me = m::mock('Mustache_Engine')->shouldDeferMissing();
        $fp = m::mock('Parsers\FieldParser');
        $or = m::mock('Console\OptionReader');
        $or->shouldReceive('getFields')->times(3)->andReturn([]);

        $entity         = 'order';
        $sourceTemplate = '/foo/bar/template.txt';
        $destinationDir = '/some/dir';
        $fileName       = 'SomeFileName.php';

        $instance = m::mock(
            'Generators\Seed[updateDatabaseSeederRunMethod,getFileName]',
            [$fs, $me, $fp, $or]
        );
        $instance->shouldReceive('getFileName')->times(5)->andReturn($fileName);
        $instance
            ->shouldReceive('updateDatabaseSeederRunMethod')
            ->times(2)
            ->with(basename($fileName, ".php"), m::any());

        //test pass
        $result = $instance->make(
            $entity,
            $sourceTemplate,
            $destinationDir,
            $fileName,
            $or
        );
        $this->assertTrue($result);

        $fs->shouldReceive('exists')
            ->with(implode(DIRECTORY_SEPARATOR, [$destinationDir, $fileName]))
            ->andReturn(true);
        $or->shouldReceive('isGenerationForced')->once()->andReturn(false);

        //test fail
        $result = $instance->make(
            $entity,
            $sourceTemplate,
            $destinationDir,
            $fileName,
            $or
        );
        $this->assertFalse($result);

        //test force make
        $or->shouldReceive('isGenerationForced')->once()->andReturn(true);
        $result = $instance->make(
            $entity,
            $sourceTemplate,
            $destinationDir,
            $fileName,
            $or
        );
        $this->assertTrue($result);
    }



    public function testUpdateDatabaseSeederRunMethod()
    {
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $me = m::mock('Mustache_Engine');
        $fp = m::mock('Parsers\FieldParser');
        $or = m::mock('Console\OptionReader');
        $or->shouldDeferMissing();

        $dir = '/some/path/name';
        $path = implode(DIRECTORY_SEPARATOR, [$dir, 'app', 'database', 'seeds', 'DatabaseSeeder.php']);
        $className = "OrderSeeder";

        //test file not found
        $fs->shouldReceive('exists')->once()->with($path)->andReturn(false);
        $instance = new Seed($fs, $me, $fp, $or);
        $this->assertFalse($instance->updateDatabaseSeederRunMethod($className, $dir));

        //test file found and class already exists
        $fs->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $content = "blah blah \$this->call('{$className}'); blah blah";
        $fs->shouldReceive('get')->once()->with($path)->andReturn($content);
        $instance = new Seed($fs, $me, $fp, $or);
        $this->assertFalse($instance->updateDatabaseSeederRunMethod($className, $dir));

        $fs->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $content = "blah blah blah blah";
        $fs->shouldReceive('get')->once()->with($path)->andReturn($content);
        $fs->shouldReceive('put')->once()->with($path, $content)->andReturn(true);
        $instance = new Seed($fs, $me, $fp, $or);
        $this->assertTrue($instance->updateDatabaseSeederRunMethod($className, $dir));
    }
}
