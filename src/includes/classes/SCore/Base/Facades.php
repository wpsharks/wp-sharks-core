<?php
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
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Pseudo-static facades.
 *
 * @since 160227 Initial release.
 */
abstract class Facades
{
    use Traits\Facades\BrandUrls;
    use Traits\Facades\CapQueries;
    use Traits\Facades\Conflicts;
    use Traits\Facades\Database;
    use Traits\Facades\Date;
    use Traits\Facades\Dependencies;
    use Traits\Facades\Fatalities;
    use Traits\Facades\Hooks;
    use Traits\Facades\Installer;
    use Traits\Facades\MenuPage;
    use Traits\Facades\Nonce;
    use Traits\Facades\Notices;
    use Traits\Facades\Options;
    use Traits\Facades\Plugin;
    use Traits\Facades\Plugins;
    use Traits\Facades\PostQueries;
    use Traits\Facades\PostTypeQueries;
    use Traits\Facades\RestAction;
    use Traits\Facades\RoleQueries;
    use Traits\Facades\StylesScripts;
    use Traits\Facades\SysOption;
    use Traits\Facades\TermQueries;
    use Traits\Facades\Theme;
    use Traits\Facades\Transients;
    use Traits\Facades\Uninstaller;
    use Traits\Facades\UrlToPostId;
    use Traits\Facades\UserQueries;
    use Traits\Facades\WcOrderItem;
}
