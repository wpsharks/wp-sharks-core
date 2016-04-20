<?php
// This file was auto-generated:
// April 20th, 2016, 12:03 pm UTC

declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Base;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Pseudo-static facades.
 *
 * @since 160227 Initial release.
 */
abstract class Facades
{
    use Traits\Facades\Conflicts;
    use Traits\Facades\Date;
    use Traits\Facades\Db;
    use Traits\Facades\Fatalities;
    use Traits\Facades\Hooks;
    use Traits\Facades\Installer;
    use Traits\Facades\MenuPage;
    use Traits\Facades\Nonce;
    use Traits\Facades\Notices;
    use Traits\Facades\Options;
    use Traits\Facades\Plugins;
    use Traits\Facades\Uninstaller;
}
