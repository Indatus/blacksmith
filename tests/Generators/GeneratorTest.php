<?php namespace Generators;


use Generators\Generator;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Mockery as m;

class GeneratorTest extends \BlacksmithTest
{

    public function testMake()
    {
        $parsedTemplate = "
            Order
            Order
            Orders
            orders
            order
        ";

        $template = '/path/to/source/template.txt';
        $destination = '/render/template/here';
        $file = 'Order.php';

        $fs = m::mock('Illuminate\Filesystem\Filesystem');
        $me = m::mock('Mustache_Engine');

        $generator = m::mock('Generators\Generator', array($fs, $me));
        $generator->shouldDeferMissing();

        $generator->shouldReceive('getFileName')
            ->twice()
            ->andReturn($file);

        $generator->shouldReceive('getTemplate')
            ->twice()
            ->with($template)
            ->andReturn($parsedTemplate);

        $outfile = implode(DIRECTORY_SEPARATOR, [$destination, $file]);

        $fs->shouldReceive('exists')
            ->once()
            ->with($outfile)->andReturn(false);

        $fs->shouldReceive('put')->once()
            ->with($outfile, $parsedTemplate)->andReturn(true);

        //first we test a file that does not exist
        $this->assertTrue($generator->make('order', $template, $destination));

        //now we mock that the file already exists
        $fs->shouldReceive('exists')
            ->once()
            ->with($outfile)->andReturn(true);

        //test making a file that exists, should return false
        $this->assertFalse($generator->make('order', $template, $destination));

        $this->assertEquals('order', strtolower($generator->getEntityName()));
    }



    public function testGetTemplateVarsForSimpleEntity()
    {
        $generator = m::mock('Generators\Generator');
        $generator->shouldDeferMissing();

        $generator->shouldReceive('getEntityName')->once()
            ->andReturn('Order');

        $generator->shouldReceive('getFileName')->once()
            ->andReturn('Order.php');

        $expected = [
            'ClassName'  => 'Order',
            'Entity'     => 'Order',
            'Entities'   => 'Orders',
            'collection' => 'orders',
            'instance'   => 'order',
        ];

        $this->assertEquals($expected, $generator->getTemplateVars());
    }



    public function testGetTemplateVarsForComplexEntity()
    {
        $generator = m::mock('Generators\Generator');
        $generator->shouldDeferMissing();

        $generator->shouldReceive('getEntityName')->once()
            ->andReturn('EcommerceOrderCreator');

        $generator->shouldReceive('getFileName')->once()
            ->andReturn('EcommerceOrderCreator.php');

        $expected = [
            'ClassName'  => 'EcommerceOrderCreator',
            'Entity'     => 'EcommerceOrderCreator',
            'Entities'   => 'EcommerceOrderCreators',
            'collection' => 'ecommerce_order_creators',
            'instance'   => 'ecommerce_order_creator',
        ];

        $this->assertEquals($expected, $generator->getTemplateVars());
    }



    public function testGetTemplate()
    {
        $templateText = '
            {{ClassName}}
            Nested{{Entity}}SomethingOrAnother
            CollectionOf{{Entities}}
            ${{collection}}->call();
            $this->{{instance}}->function();
        ';

        $parsedTemplate = '
            Order
            NestedOrderSomethingOrAnother
            CollectionOfOrders
            $orders->call();
            $this->order->function();
        ';

        $template = '/path/to/source/template.txt';

        $fs = m::mock('Illuminate\Filesystem\Filesystem');

        $fs->shouldReceive('get')->once()->with($template)->andReturn($templateText);

        $generator = m::mock('Generators\Generator', array($fs, new Mustache_Engine));
        $generator->shouldDeferMissing();

        $generator->shouldReceive('getEntityName')->once()
            ->andReturn('Order');

        $generator->shouldReceive('getFileName')->once()
            ->andReturn('Order.php');

        $result = $generator->getTemplate($template);
        $this->assertEquals(
            $parsedTemplate,
            $result
        );
    }
}
