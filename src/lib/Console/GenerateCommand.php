<?php namespace Console;

/**
 * This file was copied from and inspired by a similar
 * file in the Laravel / Envoy package which is released
 * under the the MIT license.
 *
 * @see  https://github.com/laravel/envoy
 */

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Delegates\GeneratorDelegate;
use Configuration\ConfigReader;
use Generators\Generator;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;

/**
 * CLI command used to build a new Laravel app
 * and customize it with directives in a provided
 * Foreman template
 */
class GenerateCommand extends \Symfony\Component\Console\Command\Command
{

    use Command;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('generate')
                ->setDescription('Commission Blacksmith to generate some code for you')
                ->addArgument('entity', InputArgument::REQUIRED, "Name of the associated entity your generating for")
                ->addArgument('what', InputArgument::REQUIRED, "What do you want to generate")
                ->addArgument('config-file', InputArgument::OPTIONAL, "Blacksmith config file");
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    protected function fire()
    {
        $filesystem = new Filesystem;

        //create a new generator class w/ a string
        $generator = "Generators\\". Str::studly($this->argument('what'));

        /**
         * @todo Refactor this to be retrieved by a factory
         */
        $delegate = new GeneratorDelegate(
            $this,
            new ConfigReader($filesystem, (empty($this->argument('config-file')) ? null : $this->argument('config-file'))),
            new $generator($filesystem, new Mustache_Engine),
            $this->input->getArguments(),
            $this->getOptions()
        );

        $delegate->run();
    }
}
