<?php namespace Factories;

use Console\OptionReader;
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

        $mcrf = m::mock('Factories\ConfigReaderFactory');
        $mcrf->shouldDeferMissing();
        $mgf = m::mock('Factories\GeneratorFactory');
        $mgf->shouldDeferMissing();
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldDeferMissing();

        //test with specific architecture
        $result = (new GeneratorDelegateFactory($mcrf, $mgf, $fs))->make($cmd, $args, new OptionReader($opts));
        $this->assertInstanceOf('Delegates\GeneratorDelegateInterface', $result);
        $this->assertInstanceOf('Delegates\GeneratorDelegate', $result);

        //test default
        $result = (new GeneratorDelegateFactory($mcrf, $mgf, $fs))->make($cmd, $args, new OptionReader([]));
        $this->assertInstanceOf('Delegates\GeneratorDelegateInterface', $result);
        $this->assertInstanceOf('Delegates\GeneratorDelegate', $result);
    }

    public function testMakesInvalidGeneratorDelegate()
    {
        $cmd     = m::mock('Console\GenerateCommand');
        $cfg     = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');
        $args    = ['entity' => 'order', 'what' => 'model', 'config-file' => $cfg];

        $mcrf = m::mock('Factories\ConfigReaderFactory');
        $mcrf->shouldDeferMissing();
        $mgf = m::mock('Factories\GeneratorFactory');
        $mgf->shouldDeferMissing();
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldDeferMissing();
        $or = new OptionReader([
                'architecture' => 'invalid'
            ]);

        $this->setExpectedException('InvalidArgumentException');
        $result = (new GeneratorDelegateFactory($mcrf, $mgf, $fs))->make($cmd, $args, $or);
    }


    public function testMakeAggregateGeneratorDelegate()
    {
        $cmd     = m::mock('Console\GenerateCommand');
        $cfg     = realpath(__DIR__.'/../../src/lib/Generators/templates/hexagonal/config.json');
        $args    = ['entity' => 'order', 'what' => 'scaffold', 'config-file' => $cfg];

        $mcrf = m::mock('Factories\ConfigReaderFactory');
        $mcrf->shouldDeferMissing();
        $mgf = m::mock('Factories\GeneratorFactory');
        $mgf->shouldDeferMissing();
        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $fs->shouldDeferMissing();
        $or = new OptionReader([
                'architecture' => 'hexagonal'
            ]);

        $result = (new GeneratorDelegateFactory($mcrf, $mgf, $fs))->make($cmd, $args, $or);
        $this->assertInstanceOf('Delegates\AggregateGeneratorDelegate', $result, 'expected AggregateGeneratorDelegate');
    }
}
