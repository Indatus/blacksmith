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
use Factories\GeneratorDelegateFactory;
use Factories\ConfigReaderFactory;
use Factories\GeneratorFactory;
use Illuminate\Filesystem\Filesystem;

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
        $generatorDelegateFactory = new GeneratorDelegateFactory(
            new ConfigReaderFactory,
            new GeneratorFactory,
            new Filesystem
        );
        
        $delegate = $generatorDelegateFactory->make(
            $this,
            $this->input->getArguments(),
            $this->getOptions()
        );

        $delegate->run();
    }
}
