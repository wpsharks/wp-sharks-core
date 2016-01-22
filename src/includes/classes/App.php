<?php
declare (strict_types = 1);
namespace WebSharks\WpCore\Classes;

use WebSharks\WpCore\Classes\Utils;
use WebSharks\WpCore\Functions as w;
use WebSharks\WpCore\Interfaces;
use WebSharks\WpCore\Traits;
#
use WebSharks\Core\WpCore\Functions as c;
use WebSharks\Core\WpCore\Classes\Exception;
use WebSharks\Core\WpCore\Classes as CoreClasses;
use WebSharks\Core\WpCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpCore\Traits as CoreTraits;

/**
 * Application.
 *
 * @since 16xxxx Initial release.
 */
class App extends CoreClasses\App
{
    /**
     * Version.
     *
     * @since 16xxxx
     *
     * @type string Version.
     */
    const VERSION = '160122'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $instance Instance args (highest precedence).
     */
    public function __construct(array $instance = [])
    {
        $instance_base = [
            'di' => [
                'default_rule' => [
                    'new_instances' => [

                    ],
                ],
            ],
        ];
        parent::__construct($instance_base, $instance);
    }
}
