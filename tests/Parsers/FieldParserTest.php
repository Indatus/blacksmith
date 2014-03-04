<?php namespace Parsers;


use Parsers\FieldParser;

class FieldParserTest extends \BlacksmithTest
{
    protected $parser;


    public function setUp()
    {
        parent::setUp();
        $this->parser = new FieldParser;
    }


    public function testParsing()
    {
        $this->assertEquals([], $this->parser->parse(null));

        $this->assertEquals(
            [
                'name' => ['type' => 'string']
            ],
            $this->parser->parse('name:string')
        );


        $this->assertEquals(
            [
                'name' => ['type' => 'string'],
                'age'  => ['type' => 'integer']
            ],
            $this->parser->parse('name:string, age:integer')
        );

        
        $this->assertEquals(
            [
                'name' => ['type' => 'string'],
                'age'  => ['type' => 'integer']
            ],
            $this->parser->parse('name:string, age:integer')
        );


        $this->assertEquals(
            [
                'name' => ['type' => 'string', 'decorators' => ['nullable']],
                'age'  => ['type' => 'integer']
            ],
            $this->parser->parse('name:string:nullable, age:integer')
        );


        $this->assertEquals(
            [
                'name' => ['type' => 'string', 'args' => '15', 'decorators' => ['nullable']],
            ],
            $this->parser->parse('name:string(15):nullable')
        );


        $this->assertEquals(
            [
                'column' => ['type' => 'double', 'args' => '15,8', 'decorators' => ['nullable', 'default(10)']],
                'age'  => ['type' => 'integer']
            ],
            $this->parser->parse('column:double(15,8):nullable:default(10), age:integer')
        );
    }
}
