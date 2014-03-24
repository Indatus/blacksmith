<?php namespace Configuration;


use Configuration\ConfigReader;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class OptionReaderTest extends \BlacksmithTest
{

    public function testFields()
    {
        $optionReader = new OptionReader(['f', 'fields="username:string:unique, age:integer:nullable"']);
        $this->assertTrue($optionReader->isGenerationForced());
        $this->assertEquals($optionReader->getFields(), 'username:string:unique, age:integer:nullable');
    }

    public function testFieldsDefault()
    {
        $optionReader = new OptionReader([]);
        $this->assertFalse($optionReader->isGenerationForced());
        $this->assertEquals($optionReader->getFields(), []);
    }
}
