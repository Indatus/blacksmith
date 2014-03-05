<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class ViewShowTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new ViewShow(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }



    public function testGetTemplateVars()
    {
        $generator = m::mock('Generators\ViewShow');
        $generator->shouldDeferMissing();

        $generator->shouldReceive('getEntityName')->once()
            ->andReturn('Order');

        $fieldData = [
            'name' => ['type' => 'string'],
            'age'  => ['type' => 'integer']
        ];

        $generator->shouldReceive('getFieldData')->once()
            ->andReturn($fieldData);

        $headings = array_keys($fieldData);
        $cells    = ["\$order->name"];
        $cells[]  = "\$order->age";

        $expected = [
            'Entity'     => 'Order',
            'Entities'   => 'Orders',
            'collection' => 'orders',
            'instance'   => 'order',
            'fields'     => $fieldData,
            'headings'   => $headings,
            'cells'      => $cells
        ];

        $this->assertEquals($expected, $generator->getTemplateVars());
    }
}
