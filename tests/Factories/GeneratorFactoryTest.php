<?php namespace Factories;

use Generators\GeneratorInterface;
use Factories\GeneratorFactory;
use Mockery as m;

class GeneratorFactoryTest extends \BlacksmithTest
{

    public function testMakesValidGenerator()
    {
        $gen = GeneratorFactory::make('model');
        $this->assertInstanceOf("Generators\GeneratorInterface", $gen);
    }


    public function testThrowsExceptionForInvalidClass()
    {
        $this->setExpectedException('InvalidArgumentException');
        $gen = GeneratorFactory::make('invalid');
    }


    public function testExceptionForInvalidInterface()
    {
        $refl = m::mock('Factories\GeneratorFactory');
        $refl->shouldDeferMissing();
        $refl->shouldReceive('implementsInterface')->withAnyArgs()
            ->andReturn(false);

        $this->setExpectedException('InvalidArgumentException');
        $gen = GeneratorFactory::make('model', $refl);
    }
}
