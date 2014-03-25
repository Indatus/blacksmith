<?php namespace Delegates;

use Delegates\AggregateGeneratorDelegate;
use Console\GenerateCommand;
use Configuration\ConfigReader;
use Generators\Generator;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Mockery as m;

class AggregateGeneratorDelegateTest extends \BlacksmithTest
{
    private $command;

    private $config;

    private $generator;

    private $args;

    private $optionReader;

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
            'what'        => 'scaffold',
            'config-file' => null,
        ];

        $this->genFactory
            ->shouldReceive('make')
            ->with($this->args['what'])
            ->andReturn($this->generator);

        $this->optionReader = m::mock('Console\OptionReader');
        $this->optionReader->shouldReceive('isGenerationForced')->andReturn(false);
        $this->optionReader->shouldReceive('getFields')->andReturn([]);
        $this->optionReader->shouldDeferMissing();
    }



    public function testRunWithInvalidConfigAndFails()
    {
        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(false);

        $this->command->shouldReceive('comment')->once()
            ->with('Error', 'The loaded configuration file is invalid', true);

        $delegate = new AggregateGeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->optionReader
        );
        $this->assertFalse($delegate->run());
    }



    public function testRunWithInvalidGenerationRequestAndFails()
    {
        //change the args to have an invalid generation request
        $requested = 'something-invalid';
        $this->args['what'] = $requested;

        //mock valid options
        $options = [
            'scaffold' => [
                "model",
                "controller",
                "seed",
                "migration_create",
                "view_create",
                "view_update",
                "view_show",
                "view_index",
                "form",
                "unit_test",
                "functional_test",
                "service_creator",
                "service_updater",
                "service_destroyer",
                "validator"
            ]
        ];

        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(true);

        //return possible aggregates that include the requested
        $this->config->shouldReceive('getAvailableAggregates')->once()
            ->andReturn(array_keys($options));

        $this->command->shouldReceive('comment')->once()
            ->with('Error', "{$requested} is not a valid option", true);

        $this->command->shouldReceive('comment')->once()
            ->with('Error Details', "Please choose from: ". implode(", ", array_keys($options)), true);

        $delegate = new AggregateGeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->optionReader
        );
        $this->assertFalse($delegate->run());
    }


    public function testRunWithValidArgumentsShouldSucceed()
    {
        //mock valid options
        $options = $this->getValidOptions();
        $cnt = count($options['scaffold']);

        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(true);

        //return possible aggregates that include the requested
        $this->config->shouldReceive('getAvailableAggregates')->once()
            ->andReturn(array_keys($options));

        $this->config->shouldReceive('getAggregateValues')->once()
            ->with('scaffold')
            ->andReturn($options['scaffold']);

        $baseDir = '/path/to';
        $this->config->shouldReceive('getConfigDirectory')->times($cnt)
            ->andReturn($baseDir);

        //settings to be returned by getConfigValue below
        $settings = [
            ConfigReader::CONFIG_VAL_TEMPLATE  => 'template.txt',
            ConfigReader::CONFIG_VAL_DIRECTORY => '/path/to/dir',
            ConfigReader::CONFIG_VAL_FILENAME  => 'Output.php'
        ];


        foreach ($options['scaffold'] as $to_generate) {

            $this->config->shouldReceive('getConfigValue')
                ->withAnyArgs()
                ->andReturn($settings);

            $this->genFactory->shouldReceive('make')->once()
                ->with($to_generate, $this->optionReader)
                ->andReturn($this->generator);

            //mock call to generator->make()
            $this->generator->shouldReceive('make')
                ->withAnyArgs()->andReturn(true);

            $dest = '/path/to/dir/Output.php';
            $this->generator->shouldReceive('getTemplateDestination')
                ->andReturn($dest);

            $this->command->shouldReceive('comment')->withAnyArgs();

        }//end foreach

        $delegate = new AggregateGeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->optionReader
        );

        $this->assertTrue($delegate->run());
    }


    public function testRunWithFalseArgumentsShouldSucceed()
    {
        //mock valid options
        $options = $this->getValidOptions();

        $cnt = count($options['scaffold']);

        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(true);

        //return possible aggregates that include the requested
        $this->config->shouldReceive('getAvailableAggregates')->once()
            ->andReturn(array_keys($options));

        $this->config->shouldReceive('getAggregateValues')->once()
            ->with('scaffold')
            ->andReturn($options['scaffold']);

        $baseDir = '/path/to';
        $this->config->shouldReceive('getConfigDirectory')->times($cnt)
            ->andReturn($baseDir);

        //settings to be returned by getConfigValue below
        $settings = [
            ConfigReader::CONFIG_VAL_TEMPLATE  => 'template.txt',
            ConfigReader::CONFIG_VAL_DIRECTORY => '/path/to/dir',
            ConfigReader::CONFIG_VAL_FILENAME  => 'Output.php'
        ];

        $options['scaffold']['view_show'] = false;
        foreach ($options['scaffold'] as $idx => $to_generate) {
            $configValue = $settings;
            if ($to_generate === false) {
                $this->command->shouldReceive('comment')
                    ->with(
                        "Blacksmith",
                        "I skipped \"".$to_generate.'"',
                        true
                    );
                continue;
            }

            $this->config->shouldReceive('getConfigValue')
                ->withAnyArgs()
                ->andReturn($configValue);

            $this->genFactory->shouldReceive('make')->once()
                ->with($to_generate, $this->optionReader)
                ->andReturn($this->generator);

            //mock call to generator->make()
            $this->generator->shouldReceive('make')
                ->withAnyArgs()->andReturn(true);

            $dest = '/path/to/dir/Output.php';
            $this->generator->shouldReceive('getTemplateDestination')
                ->andReturn($dest);

            $this->command->shouldReceive('comment')->withAnyArgs();

        }//end foreach

        $delegate = new AggregateGeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->optionReader
        );
        $this->assertTrue($delegate->run());
    }

    public function testRunWithValidArgumentsShouldFail()
    {
        //mock valid options
        $options = $this->getValidOptions();
        $cnt = count($options['scaffold']);

        $this->config->shouldReceive('validateConfig')->once()
            ->andReturn(true);

        //return possible aggregates that include the requested
        $this->config->shouldReceive('getAvailableAggregates')->once()
            ->andReturn(array_keys($options));

        $this->config->shouldReceive('getAggregateValues')->once()
            ->with('scaffold')
            ->andReturn($options['scaffold']);

        $baseDir = '/path/to';
        $this->config->shouldReceive('getConfigDirectory')->times($cnt)
            ->andReturn($baseDir);

        //settings to be returned by getConfigValue below
        $settings = [
            ConfigReader::CONFIG_VAL_TEMPLATE  => 'template.txt',
            ConfigReader::CONFIG_VAL_DIRECTORY => '/path/to/dir',
            ConfigReader::CONFIG_VAL_FILENAME  => 'Output.php'
        ];


        foreach ($options['scaffold'] as $to_generate) {

            $this->config->shouldReceive('getConfigValue')
                ->withAnyArgs()
                ->andReturn($settings);

            $this->genFactory->shouldReceive('make')->once()
                ->with($to_generate, $this->optionReader)
                ->andReturn($this->generator);

            //mock call to generator->make()
            $this->generator->shouldReceive('make')
                ->withAnyArgs()->andReturn(false);


            $this->command->shouldReceive('comment')
                ->with(
                    "Blacksmith",
                    "An unknown error occurred, nothing was generated for {$to_generate}",
                    true
                );

        }//end foreach

        $delegate = new AggregateGeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->optionReader
        );
        $this->assertTrue($delegate->run());
    }


    public function testUpdateRoutesFile()
    {
        $name = 'orders';
        $dir = '/some/path';
        $routes = implode(DIRECTORY_SEPARATOR, [$dir, 'app', 'routes.php']);
        $data = "\n\nRoute::resource('" . $name . "', '" . ucwords($name) . "Controller');";

        $this->filesystem->shouldReceive('exists')->once()
            ->with($routes)
            ->andReturn(true);

        $this->filesystem->shouldReceive('append')->once()
            ->with($routes, $data);

        $delegate = new AggregateGeneratorDelegate(
            $this->command,
            $this->config,
            $this->genFactory,
            $this->filesystem,
            $this->args,
            $this->optionReader
        );
        $delegate->updateRoutesFile($name, $dir);
    }

    private function getValidOptions()
    {
        return [
                'scaffold' => [
                    "model",
                    "controller",
                    "seed",
                    "migration_create",
                    "view_create",
                    "view_update",
                    "view_show",
                    "view_index",
                    "form",
                    "unit_test",
                    "functional_test",
                    "service_creator",
                    "service_updater",
                    "service_destroyer",
                    "validator"
                ]
            ];
    }
}
