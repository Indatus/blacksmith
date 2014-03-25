<?php namespace Console;


use Configuration\ConfigReader;
use Factories\GeneratorDelegateFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class OptionReaderTest extends \BlacksmithTest
{

    public function testFields()
    {
        $optionReader = new OptionReader([
                'f',
                'architecture' => 'test',
                'fields' => 'username:string:unique, age:integer:nullable'
            ]);
        $this->assertTrue($optionReader->isGenerationForced());
        $this->assertEquals($optionReader->getArchitecture(), 'test');
        $this->assertEquals($optionReader->getFields(), 'username:string:unique, age:integer:nullable');
    }

    public function testFieldsDefault()
    {
        $optionReader = new OptionReader([]);
        $this->assertFalse($optionReader->isGenerationForced());
        $this->assertEquals($optionReader->getArchitecture(), GeneratorDelegateFactory::ARCH_HEXAGONAL);
        $this->assertEquals($optionReader->getFields(), []);
    }
}
