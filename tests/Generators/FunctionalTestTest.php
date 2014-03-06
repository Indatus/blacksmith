<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Parsers\FieldParser;
use Mustache_Engine;
use Mockery as m;

class FunctionalTestTest extends \BlacksmithTest
{

    public function testParentClass()
    {
        $instance = new FunctionalTest(
            new Filesystem,
            new Mustache_Engine,
            new FieldParser
        );
        $this->assertInstanceOf("Generators\Generator", $instance);
    }

    public function testGetAttributeMockValue()
    {
        $g = m::mock('Generators\FunctionalTest');
        $g->shouldDeferMissing();

        //check integers
        $this->assertTrue(
            is_int($g->getAttributeMockValue('integer')),
            "Expected integer to return integer val"
        );

        //check big integers
        $this->assertTrue(
            is_int($g->getAttributeMockValue('bigInteger')),
            "Expected bigInteger to return integer val"
        );
        $this->assertTrue(
            ($g->getAttributeMockValue('bigInteger') <= PHP_INT_MAX),
            "Expected bigInteger to be less than or equal to PHP_INT_MAX"
        );
        

        //check strings
        $stringVal = $g->getAttributeMockValue('string');
        $this->assertTrue(
            is_string($stringVal),
            "Expected string to be a string"
        );
        $this->assertTrue(
            ($stringVal[0] == "'" && $stringVal[strlen($stringVal)-1] == "'"),
            "Expected string value to be sigle quoted"
        );

        //check decimals
        $this->assertTrue(
            is_double($g->getAttributeMockValue('decimal')),
            "Expected decimal value to be a double"
        );

        //check floats
        $this->assertTrue(
            is_float($g->getAttributeMockValue('float')),
            "Expected float value to be a float"
        );
        
        //check timestamps
        $this->assertTrue(
            is_int($g->getAttributeMockValue('timestamp')),
            "Expected timestamp to be an int"
        );

        //check dates
        $date_regex = '/(19|20)\d\d[- -.](0[1-9]|1[012])[- -.](0[1-9]|[12][0-9]|3[01])/';
        $dateVal    = $g->getAttributeMockValue('date');
        $this->assertTrue(
            preg_match($date_regex, str_replace("'", "", $dateVal)) > 0,
            "Expected date [{$dateVal}] to be in YYYY-MM-DD format"
        );
        $this->assertTrue(
            ($dateVal[0] == "'" && $dateVal[strlen($dateVal)-1] == "'"),
            "Expected date [{$dateVal}] value to be sigle quoted"
        );

        //test datetimes
        $datetime_regex = '/(19|20)\d\d[- -.](0[1-9]|1[012])[- -.](0[1-9]|[12][0-9]|3[01])\s(0[0-9]|1[0-9]|2[0123]):([0-5][0-9]):([0-5][0-9])/';
        $dateTimeVal    = $g->getAttributeMockValue('dateTime');
        $this->assertTrue(
            preg_match($datetime_regex, str_replace("'", "", $dateTimeVal)) > 0,
            "Expected dateTime [{$dateTimeVal}] to be in YYYY-MM-DD HH:ii:ss format"
        );
        $this->assertTrue(
            ($dateTimeVal[0] == "'" && $dateTimeVal[strlen($dateTimeVal)-1] == "'"),
            "Expected dateTime [{$dateTimeVal}] value to be sigle quoted"
        );

        //test text values
        $textVal = $g->getAttributeMockValue('text');
        $this->assertTrue(
            is_string($textVal),
            "Expected text value to be a string"
        );
        $this->assertTrue(
            ($textVal[0] == "'" && $textVal[strlen($textVal)-1] == "'"),
            "Expected text value to be sigle quoted"
        );
        $this->assertTrue(
            strlen($textVal) > 25,
            "Expected text value to be greater than 25 characters"
        );

        //test booleans
        $this->assertTrue(
            is_bool($g->getAttributeMockValue('boolean')),
            "Expected boolean value to be bool"
        );
    }
}
