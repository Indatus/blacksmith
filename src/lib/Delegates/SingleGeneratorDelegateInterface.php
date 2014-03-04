<?php namespace Delegates;

use Console\GenerateCommand;
use Configuration\ConfigReader;
use Generators\Generator;

interface SingleGeneratorDelegateInterface
{
    public function __construct(GenerateCommand $cmd, ConfigReader $cfg, Generator $gen, array $command_args, array $options = []);

    public function run();
}
