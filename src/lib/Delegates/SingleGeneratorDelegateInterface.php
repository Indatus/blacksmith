<?php namespace Delegates;

use Console\GenerateCommand;
use Configuration\ConfigReaderInterface;
use Generators\GeneratorInterface;

interface SingleGeneratorDelegateInterface
{
    public function __construct(
        GenerateCommand $cmd,
        ConfigReaderInterface $cfg,
        GeneratorInterface $gen,
        array $command_args,
        array $options = []
    );

    public function run();
}
