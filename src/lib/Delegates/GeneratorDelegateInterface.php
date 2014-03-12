<?php namespace Delegates;

use Console\GenerateCommand;
use Configuration\ConfigReaderInterface;
use Factories\GeneratorFactory;
use Illuminate\Filesystem\Filesystem;

interface GeneratorDelegateInterface
{
    public function __construct(
        GenerateCommand $cmd,
        ConfigReaderInterface $cfg,
        GeneratorFactory $gen,
        Filesystem $filesystem,
        array $command_args,
        array $options = []
    );

    public function run();
}
