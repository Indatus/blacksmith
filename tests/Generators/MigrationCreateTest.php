<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Generators\MigrationCreate;
use Mockery as m;

class MigrationCreateTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new MigrationCreate(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }



    public function testGetTemplateVarsForSimpleEntity()
    {
        $generator = m::mock('Generators\MigrationCreate');
        $generator->shouldDeferMissing();

        $generator->shouldReceive('getEntityName')->once()
            ->andReturn('Order');

        $fieldData = [
            'name' => ['type' => 'string'],
            'age'  => ['type' => 'integer']
        ];

        $generator->shouldReceive('getFieldData')->once()
            ->andReturn($fieldData);

        $columns    = [];
        $columns[]  = "\$table->increments('id');";
        $columns[]  = "\$table->string('name');";
        $columns[]  = "\$table->integer('age');";
        $columns[]  = "\$table->timestamps();";
        $columnData = implode("\n\t\t\t", $columns);

        $expected = [
            'Entity'     => 'Order',
            'Entities'   => 'Orders',
            'collection' => 'orders',
            'instance'   => 'order',
            'fields'     => $fieldData,
            'columns'    => $columnData,
            'migration_timestamp' => date('Y_m_d_His')
        ];

        $this->assertEquals($expected, $generator->getTemplateVars());
    }
}
