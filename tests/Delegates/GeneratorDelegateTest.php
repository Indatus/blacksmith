<?php namespace Delegates;

use Delegates\GeneratorDelegate;
use Console\GenerateCommand;
use Configuration\ConfigReader;
use Generators\Generator;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Mockery as m;

class GeneratorDelegateTest extends \BlacksmithTest
{
    private $command;

    private $config;

    private $generator;

    private $args;

    private $options;

    public function setUp()
    {
        parent::setUp();
        $this->command = m::mock('Console\GenerateCommand');
        $this->config = m::mock('Configuration\ConfigReader');
        $this->generator = m::mock('Generators\Generator');
        $this->genFactory = m::mock('Factories\GeneratorFactory');
        $this->filesystem = m::mock('Illuminate\Filesystem\Filesystem');
        $this->filesystem->shouldDeferMissing();
        $this->args = [
            'command'     => 'generate',
            'entity'      => 'Order',
            'what'        => 'model',
            'config-file' => null,
        ];

        $this->genFactory
            ->shouldReceive('make')
            ->with($this->args['what'])
            ->andReturn($this->generator);

        $this->options = [];
    }



    public function testRunWithInvalidConfigAndFails()
    {
        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(false);

        $this->command->shouldReceive('comment')->once()
            ->with('Error', 'The loaded configuration file is invalid', true);

        $delegate = new GeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->options
        );
        $this->assertFalse($delegate->run());
    }



    public function testRunWithInvalidGenerationRequestAndFails()
    {
        //change the args to have an invalid generation request
        $requested = 'something-invalid';
        $options = ['foo', 'bar', 'biz'];

        $this->args['what'] = $requested;

        $this->genFactory
            ->shouldReceive('make')
            ->with($requested)
            ->andThrow("InvalidArgumentException");

        $this->setExpectedException('InvalidArgumentException');
        
        $delegate = new GeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->options
        );
        $this->assertFalse($delegate->run());
    }


    public function testRunWithValidArgumentsShouldSucceed()
    {
        //mock valid options
        $options = ['model', 'controller'];

        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(true);

        //return possible generators that include the requested
        $this->config->shouldReceive('getAvailableGenerators')->once()
            ->andReturn($options);

        $this->config->shouldReceive('getConfigType')->once();

        $baseDir = '/path/to';
        $this->config->shouldReceive('getConfigDirectory')->once()
            ->andReturn($baseDir);

        //settings to be returned by getConfigValue below
        $settings = [
            ConfigReader::CONFIG_VAL_TEMPLATE  => 'template.txt',
            ConfigReader::CONFIG_VAL_DIRECTORY => '/path/to/dir',
            ConfigReader::CONFIG_VAL_FILENAME  => 'Output.php'
        ];

        $this->config->shouldReceive('getConfigValue')->once()
            ->with($this->args['what'])
            ->andReturn($settings);

        //mock call to generator->make()
        $this->generator->shouldReceive('make')->once()
            ->with(
                $this->args['entity'],
                implode(DIRECTORY_SEPARATOR, [$baseDir, $settings[ConfigReader::CONFIG_VAL_TEMPLATE]]),
                $settings[ConfigReader::CONFIG_VAL_DIRECTORY],
                $settings[ConfigReader::CONFIG_VAL_FILENAME],
                null
            )->andReturn(true);

        $dest = '/path/to/dir/Output.php';
        $this->generator->shouldReceive('getTemplateDestination')->once()
            ->andReturn($dest);

        $this->command->shouldReceive('comment')->once()
            ->with('Blacksmith', "Success, I generated the code for you in {$dest}");

        $delegate = new GeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->options
        );
        $this->assertTrue($delegate->run());
    }



    public function testRunWithValidArgumentsButGeneratorFailure()
    {
        //mock valid options
        $options = ['model', 'controller'];

        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(true);

        //return possible generators that include the requested
        $this->config->shouldReceive('getAvailableGenerators')->once()
            ->andReturn($options);

        $this->config->shouldReceive('getConfigType')->once();

        $baseDir = "/path/to";
        $this->config->shouldReceive('getConfigDirectory')->once()
            ->andReturn($baseDir);

        //settings to be returned by getConfigValue below
        $settings = [
            ConfigReader::CONFIG_VAL_TEMPLATE  => 'template.txt',
            ConfigReader::CONFIG_VAL_DIRECTORY => '/path/to/dir',
            ConfigReader::CONFIG_VAL_FILENAME  => 'Output.php'
        ];

        $this->config->shouldReceive('getConfigValue')->once()
            ->with($this->args['what'])
            ->andReturn($settings);

        //mock call to generator->make()
        $this->generator->shouldReceive('make')->once()
            ->with(
                $this->args['entity'],
                implode(DIRECTORY_SEPARATOR, [$baseDir, $settings[ConfigReader::CONFIG_VAL_TEMPLATE]]),
                $settings[ConfigReader::CONFIG_VAL_DIRECTORY],
                $settings[ConfigReader::CONFIG_VAL_FILENAME],
                null
            )->andReturn(false);

        $this->command->shouldReceive('comment')->once()
            ->with('Blacksmith', "An unknown error occured, nothing was generated", true);

        $delegate = new GeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->options
        );
        $this->assertFalse($delegate->run());
    }
}
