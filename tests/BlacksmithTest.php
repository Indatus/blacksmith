<?php

use Mockery as m;

abstract class BlacksmithTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
