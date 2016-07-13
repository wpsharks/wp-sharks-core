<?php
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
 * App utils.
 *
 * @since 160710 App utils.
 */
class Apps extends Classes\SCore\Base\Core
{
    /**
     * Array of all apps.
     *
     * @since 160710 App utils.
     *
     * @type array Array of all apps.
     */
    protected $apps;

    /**
     * Array of all apps.
     *
     * @since 160710 App utils.
     *
     * @type array Array of all apps.
     */
    protected $apps_by_type;

    /**
     * Array of all apps.
     *
     * @since 160710 App utils.
     *
     * @type array Array of all apps.
     */
    protected $apps_by_slug;

    /**
     * Class constructor.
     *
     * @since 160710 App utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        if (!$this->App->is_core) {
            throw $this->c::issue('Core only.');
        }
        $this->apps         = [];
        $this->apps_by_type = [];
        $this->apps_by_slug = [];
    }

    /**
     * Adds an app instance.
     *
     * @since 160710 App utils.
     *
     * @param Classes\App $App Instance.
     */
    public function add(Classes\App $App)
    {
        $this->apps[]                                                                     = &$App;
        $this->apps_by_type[$App->Config->§specs['§type']][$App->Config->©brand['©slug']] = &$App;
        $this->apps_by_slug[$App->Config->©brand['©slug']]                                = &$App;
    }

    /**
     * Get app instances.
     *
     * @since 160710 App utils.
     *
     * @return Classes\App[] Apps.
     */
    public function get(): array
    {
        return $this->apps;
    }

    /**
     * Get app instances.
     *
     * @since 160710 App utils.
     *
     * @return Classes\App[] Apps.
     */
    public function getByType(): array
    {
        return $this->apps_by_type;
    }

    /**
     * Get app instances.
     *
     * @since 160710 App utils.
     *
     * @return Classes\App[] Apps.
     */
    public function getBySlug(): array
    {
        return $this->apps_by_slug;
    }
}
