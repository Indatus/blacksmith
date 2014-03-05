<?php namespace Factories;

use Factories\MigrationColumnFactory;
use Mockery as m;

class MigrationColumnFactoryTest extends \BlacksmithTest
{

    public function testGeneratesSimpleColumn()
    {
        $input = [
            'name' => ['type' => 'string']
        ];

        $output = ["\$table->string('name');"];

        $this->assertEquals($output, MigrationColumnFactory::make($input));
    }


    public function testGeneratesMultipleSimpleColumns()
    {
        $input = [
            'name' => ['type' => 'string'],
            'age'  => ['type' => 'integer']
        ];

        $output   = ["\$table->string('name');"];
        $output[] = "\$table->integer('age');";

        $this->assertEquals($output, MigrationColumnFactory::make($input));
    }


    public function testGeneratesMultiColumnWithDecorator()
    {
        $input = [
            'name' => ['type' => 'string', 'decorators' => ['nullable']],
            'age'  => ['type' => 'integer']
        ];

        $output   = ["\$table->string('name')->nullable();"];
        $output[] = "\$table->integer('age');";

        $this->assertEquals($output, MigrationColumnFactory::make($input));
    }


    public function testGeneratesColumnWithArgsAndDecorators()
    {
        $input = [
            'name' => [
                'type' => 'string',
                'args' => '15',
                'decorators' => ['nullable']
            ]
        ];

        $output = ["\$table->string('name', 15)->nullable();"];

        $this->assertEquals($output, MigrationColumnFactory::make($input));
    }


    public function testGeneratesMultiColWithArgsAndDecorators()
    {
        $input = [
            'average' => [
                'type' => 'double',
                'args' => '15,8',
                'decorators' => ['nullable', 'default(10)']],
            'age'  => [
                'type' => 'integer'
            ]
        ];

        $output   = ["\$table->double('average', 15,8)->nullable()->default(10);"];
        $output[] = "\$table->integer('age');";

        $this->assertEquals($output, MigrationColumnFactory::make($input));
    }
}
