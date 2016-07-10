<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

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
 * Dashboard menu page utils.
 *
 * @since 160524 Dashboard menu page utils.
 */
class DbMenuPage extends Classes\SCore\Base\Core
{
    /**
     * On admin menu.
     *
     * @since 160708 Dashboard menu page utils.
     *
     * @param array $args Configuration args.
     */
    public function onAdminMenu()
    {
        $this->s::addMenuPageItem([
            'auto_prefix'   => false,
            'parent_slug'   => 'index.php',
            'page_title'    => $this->App::CORE_CONTAINER_NAME,
            'menu_title'    => $this->App::CORE_CONTAINER_NAME.' <i class="sharkicon sharkicon-wp-sharks-fin"></i>',
            'template_file' => 's-core/menu-pages/dashboard/default.php',

            'tabs' => [
                'default'      => $this->App::CORE_CONTAINER_NAME,
                'license-keys' => __('License Keys', 'wp-sharks-core'),
            ],
        ]);
    }
}
