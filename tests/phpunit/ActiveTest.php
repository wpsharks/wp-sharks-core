<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\WpSharks\Core\Classes\AppFacades as a;
use WebSharks\WpSharks\Core\Classes\SCoreFacades as s;
use WebSharks\WpSharks\Core\Classes\CoreFacades as c;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

class ActiveTest extends \PHPUnit_Framework_TestCase
{
    public function testActive()
    {
        $this->assertSame(true, c::app() instanceof Classes\App);
    }
}
