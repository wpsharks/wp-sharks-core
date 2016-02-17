<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils;
use WebSharks\WpSharks\Core\Functions as w;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

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
    const VERSION = '160217'; //v//

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
