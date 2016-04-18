<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

use WebSharks\WpSharks\Core\Classes\App;

class ActiveTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->App = $GLOBALS[App::class];
    }

    public function testActive()
    {
        $this->assertSame(true, $this->App instanceof App);
    }
}
