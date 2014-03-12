<?php namespace Configuration;


use Configuration\ConfigReader;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class ConfigReaderTest extends \BlacksmithTest
{
    public function testReadGivenConfig()
    {
        $path = '/path/to/config.json';
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('exists')->once()->with($path)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($path);

        $reader = new ConfigReader($fs, $path);
    }



    public function testReadDefaultConfig()
    {
        $path = __DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json';
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs();

        $reader = new ConfigReader($fs);

        $dirExp = explode(DIRECTORY_SEPARATOR, $reader->getConfigDirectory());
        $this->assertEquals(
            'hexagonal',
            $dirExp[count($dirExp)-1]
        );
    }



    public function testValidConfig()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $configArr = json_decode($json, true);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);

        $this->assertTrue($reader->validateConfig());
    }


    public function testGetAvailableGenerators()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);

        $this->assertTrue(
            is_array(
                $reader->getAvailableGenerators(
                    ConfigReader::CONFIG_TYPE_HEXAGONAL
                )
            )
        );

        $this->assertEquals(
            ConfigReader::CONFIG_TYPE_HEXAGONAL,
            $reader->getConfigType()
        );

        $this->assertFalse(
            $reader->getAvailableGenerators('foo')
        );
    }



    public function testInvalidConfigWithMissingConfigType()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $configArr = json_decode($json, true);

        $missing = $configArr;
        unset($missing[ConfigReader::CONFIG_TYPE_KEY]);
        $missingJson = json_encode($missing, JSON_UNESCAPED_SLASHES);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($missingJson);

        $reader = new ConfigReader($fs);
        $this->assertFalse($reader->validateConfig());
    }



    public function testInvalidConfigWithMissingRequiredKey()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $configArr = json_decode($json, true);

        $missing = $configArr;
        unset($missing[ConfigReader::CONFIG_KEY_MODEL]);
        $missingJson = json_encode($missing, JSON_UNESCAPED_SLASHES);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($missingJson);

        $reader = new ConfigReader($fs);
        $this->assertFalse($reader->validateConfig());
    }



    public function testInvalidConfigWithMissingRequiredTemplateSubKey()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $configArr = json_decode($json, true);

        $missing = $configArr;
        unset($missing[ConfigReader::CONFIG_KEY_MODEL][ConfigReader::CONFIG_VAL_TEMPLATE]);
        $missingJson = json_encode($missing, JSON_UNESCAPED_SLASHES);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($missingJson);

        $reader = new ConfigReader($fs);
        $this->assertFalse($reader->validateConfig());
    }



    public function testInvalidConfigWithMissingRequiredDirectorySubKey()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $configArr = json_decode($json, true);

        $missing = $configArr;
        unset($missing[ConfigReader::CONFIG_KEY_MODEL][ConfigReader::CONFIG_VAL_DIRECTORY]);
        $missingJson = json_encode($missing, JSON_UNESCAPED_SLASHES);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($missingJson);

        $reader = new ConfigReader($fs);
        $this->assertFalse($reader->validateConfig());
    }



    public function testInvalidConfigWithMissingRequiredFilenameSubKey()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $configArr = json_decode($json, true);

        $missing = $configArr;
        unset($missing[ConfigReader::CONFIG_KEY_MODEL][ConfigReader::CONFIG_VAL_FILENAME]);
        $missingJson = json_encode($missing, JSON_UNESCAPED_SLASHES);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($missingJson);

        $reader = new ConfigReader($fs);
        $this->assertFalse($reader->validateConfig());
    }


    public function testGetConfigValueShouldPass()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);
        $result = $reader->getConfigValue(ConfigReader::CONFIG_KEY_MODEL);
        
        $this->assertTrue(is_array($result));
    }


    public function testGetConfigValueShouldFail()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);
        $result = $reader->getConfigValue('something-invalid');
        $this->assertFalse($result);
    }


    public function testGetAvailableAggregates()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);
        $result = $reader->getAggregateValues(ConfigReader::CONFIG_AGG_KEY_SCAFFOLD);
        
        $this->assertTrue(is_array($result));
    }


    public function getAvailableAggregates()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);
        $result = $reader->getAvailableAggregates();
        
        $this->assertEquals(['scaffold'], $result);
    }



    public function testGetAggregateShoulFail()
    {
        $path = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');

        $json = file_get_contents($path);

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive('get')->once()->withAnyArgs()
            ->andReturn($json);

        $reader = new ConfigReader($fs);
        $result = $reader->getAggregateValues('something-invalid');
        $this->assertFalse($result);
    }
}
