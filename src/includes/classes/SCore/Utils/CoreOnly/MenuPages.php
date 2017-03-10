<?php
/**
 * Menu page utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils\CoreOnly;

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
 * Menu page utils.
 *
 * @since 160524 Core menu page utils.
 */
class MenuPages extends Classes\SCore\Base\Core
{
    /**
     * Class constructor.
     *
     * @since 160710 License key utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        if (!$this->App->is_core) {
            throw $this->c::issue('Core only.');
        }
    }

    /**
     * On network admin menu.
     *
     * @since 160715 Core menu page utils.
     *
     * @param array $args Configuration args.
     *
     * @internal Requires core to be activated network-wide.
     */
    public function onNetworkAdminMenu()
    {
        if ($this->s::getAppsByNetworkWide(true)) {
            $this->addMenuPages();
        }
    }

    /**
     * On admin menu.
     *
     * @since 160524 Core menu page utils.
     *
     * @param array $args Configuration args.
     */
    public function onAdminMenu()
    {
        $this->addMenuPages();
    }

    /**
     * Adds menu pages.
     *
     * @since 160524 Core menu page utils.
     *
     * @param array $args Configuration args.
     */
    protected function addMenuPages()
    {
        $this->s::addMenuPageItem([
            'auto_prefix'   => false,
            'parent_page'   => 'index.php',
            'page_title'    => $this->App::CORE_CONTAINER_NAME,
            'menu_title'    => $this->App::CORE_CONTAINER_NAME.' <i class="sharkicon sharkicon-wp-sharks-fin"></i>',
            'template_file' => 's-core/admin/menu-pages/dashboard/default.php',

            'tabs' => [
                'default' => __('License Keys', 'wp-sharks-core'),
                'about'   => sprintf(__('About %1$s', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)).'  <i class="sharkicon sharkicon-wp-sharks-fin"></i>',
            ],
        ]);
    }
}
