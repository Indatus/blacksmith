<?php namespace Factories;

use Factories\GeneratorDelegateFactory;
use Mockery as m;

class GeneratorDelegateFactoryTest extends \BlacksmithTest
{

    public function testMakesValidGeneratorDelegate()
    {
        $cmd     = m::mock('Console\GenerateCommand');
        $cfg     = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');
        $args    = ['entity' => 'order', 'what' => 'model', 'config-file' => $cfg];
        $opts    = ['architecture' => 'hexagonal'];

        //test with specific
        $result = GeneratorDelegateFactory::make($cmd, $args, $opts);
        $this->assertInstanceOf("Delegates\SingleGeneratorDelegateInterface", $result);
        $this->assertInstanceOf("Delegates\GeneratorDelegate", $result);

        //test default
        $result = GeneratorDelegateFactory::make($cmd, $args, []);
        $this->assertInstanceOf("Delegates\SingleGeneratorDelegateInterface", $result);
        $this->assertInstanceOf("Delegates\GeneratorDelegate", $result);
    }

    public function testMakesInvalidGeneratorDelegate()
    {
        $cmd     = m::mock('Console\GenerateCommand');
        $cfg     = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');
        $args    = ['entity' => 'order', 'what' => 'model', 'config-file' => $cfg];
        $opts    = ['architecture' => 'invalid'];

        $this->setExpectedException('InvalidArgumentException');
        $result = GeneratorDelegateFactory::make($cmd, $args, $opts);
    }
}
