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

    public function setUp()
    {
        parent::setUp();
        $this->command = m::mock('Console\GenerateCommand');
        $this->config = m::mock('Configuration\ConfigReader');
        $this->generator = m::mock('Generators\Generator');
        $this->args = [
            'command'     => 'generate',
            'entity'      => 'Order',
            'what'        => 'model',
            'config-file' => null,
        ];
    }

    public function testRunWithInvalidConfig()
    {

    }



    public function testRunWithInvalidGenerationRequest()
    {

    }


    public function testRunWithValidArguments()
    {

    }
}
