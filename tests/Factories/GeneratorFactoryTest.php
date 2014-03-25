<?php namespace Factories;

use Generators\GeneratorInterface;
use Factories\GeneratorFactory;
use Mockery as m;

class GeneratorFactoryTest extends \BlacksmithTest
{

    private $optionReader;

    public function setUp()
    {
        parent::setUp();
        $this->optionReader = m::mock('Console\OptionReader');
    }

    public function testMakesValidGenerator()
    {
        $gen = (new GeneratorFactory)->make('model', $this->optionReader);
        $this->assertInstanceOf("Generators\GeneratorInterface", $gen);
    }


    public function testThrowsExceptionForInvalidClass()
    {
        $this->setExpectedException('InvalidArgumentException');
        $gen = (new GeneratorFactory)->make('invalid', $this->optionReader);
    }


    public function testExceptionForInvalidInterface()
    {
        $refl = m::mock('Factories\GeneratorFactory');
        $refl->shouldDeferMissing();
        $refl->shouldReceive('implementsInterface')->withAnyArgs()
            ->andReturn(false);

        $this->setExpectedException('InvalidArgumentException');
        $gen = (new GeneratorFactory)->make('model', $this->optionReader, $refl);
    }
}
