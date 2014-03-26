<?php namespace Generators;

use Console\OptionReader;
use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class ViewIndexTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new ViewIndex(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser,
            new OptionReader([])
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }



    public function testGetTemplateVars()
    {
        $generator = m::mock('Generators\ViewIndex');
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
            'cells'      => $cells,
            'year'       => (new DateTime())->format('Y')
        ];

        $this->assertEquals($expected, $generator->getTemplateVars());
    }
}
