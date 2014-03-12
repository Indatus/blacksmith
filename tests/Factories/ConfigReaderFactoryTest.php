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

        $reader = (new ConfigReaderFactory)->make($path, $fs);
        $this->assertInstanceOf("Configuration\ConfigReaderInterface", $reader);
    }

    public function testMakesValidConfigReaderWithoutGivenPath()
    {
        $path = __DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json';
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs();

        $reader = (new ConfigReaderFactory)->make(null, $fs);
        $this->assertInstanceOf("Configuration\ConfigReaderInterface", $reader);

        $dirExp = explode(DIRECTORY_SEPARATOR, $reader->getConfigDirectory());
        $this->assertEquals(
            'hexagonal',
            $dirExp[count($dirExp)-1]
        );
    }
}
