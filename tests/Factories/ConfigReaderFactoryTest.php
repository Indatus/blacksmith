<?php namespace Factories;

use Configuration\ConfigReaderInterface;
use Factories\ConfigReaderFactory;
use Mockery as m;

class ConfigReaderFactoryTest extends \BlacksmithTest
{

    public function testMakesValidConfigReaderWithGivenPath()
    {
        $path = '/path/to/config.json';
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($path);

        $reader = ConfigReaderFactory::make($path, $fs);
        $this->assertInstanceOf("Configuration\ConfigReaderInterface", $reader);
    }

    public function testMakesValidConfigReaderWithoutGivenPath()
    {
        $path = realpath(__DIR__.'/../Generators/templates/hexagonal/config.json');
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->with($path);

        $reader = ConfigReaderFactory::make(null, $fs);
        $this->assertInstanceOf("Configuration\ConfigReaderInterface", $reader);
    }
}
