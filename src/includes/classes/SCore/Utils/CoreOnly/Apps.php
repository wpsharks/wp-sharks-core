<?php
/**
 * App utils.
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
     * @var array Array of all apps.
     */
    protected $apps;

    /**
     * Array of all apps.
     *
     * @since 160710 App utils.
     *
     * @var array Array of all apps.
     */
    protected $apps_by_slug;

    /**
     * Array of all apps.
     *
     * @since 160710 App utils.
     *
     * @var array Array of all apps.
     */
    protected $apps_by_type;

    /**
     * Array of all apps.
     *
     * @since 160715 App utils.
     *
     * @var array Array of all apps.
     */
    protected $apps_by_network_wide;

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
        $this->apps                 = [];
        $this->apps_by_slug         = [];
        $this->apps_by_type         = [];
        $this->apps_by_network_wide = [];
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
        $slug            = $App->Config->©brand['©slug'];
        $type            = $App->Config->§specs['§type'];
        $is_network_wide = $App->Config->§specs['§is_network_wide'] && $this->Wp->is_multisite;

        $this->apps[]                                                     = &$App;
        $this->apps_by_slug[$slug]                                        = &$App;
        $this->apps_by_type[$type][$slug]                                 = &$App;
        $this->apps_by_network_wide[(int) $is_network_wide][$type][$slug] = &$App;
    }

    /**
     * Get app instances.
     *
     * @since 160710 App utils.
     *
     * @return array Apps.
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
     * @param string|null $slug Specific slug?
     *
     * @return array|Classes\App|null Apps.
     */
    public function bySlug(string $slug = null)
    {
        if (isset($slug)) {
            return $this->apps_by_slug[$slug] ?? null;
        } else {
            return $this->apps_by_slug;
        }
    }

    /**
     * Get app instances.
     *
     * @since 160710 App utils.
     *
     * @param string|null $type Specific type?
     * @param string|null $slug Specific slug?
     *
     * @return array|Classes\App|null Apps.
     */
    public function byType(string $type = null, string $slug = null)
    {
        if (isset($type, $slug)) {
            return isset($this->apps_by_type[$type][$slug])
                ? $this->apps_by_type[$type][$slug] : null;
            //
        } elseif (isset($type) && !isset($slug)) {
            return $this->apps_by_type[$type] ?? [];
            //
        } elseif (!isset($type) && !isset($slug)) {
            return $this->apps_by_type;
        }
        throw $this->c::issue('Invalid parameters.');
    }

    /**
     * Get app instances.
     *
     * @since 160710 App utils.
     *
     * @param bool|null   $is_network_wide True or false.
     * @param string|null $type            Specific type?
     * @param string|null $slug            Specific slug?
     *
     * @return array|Classes\App|null Apps.
     */
    public function byNetworkWide(bool $is_network_wide = null, string $type = null, string $slug = null): array
    {
        if (isset($is_network_wide, $type, $slug)) {
            return isset($this->apps_by_network_wide[(int) $is_network_wide][$type][$slug])
                ? $this->apps_by_network_wide[(int) $is_network_wide][$type][$slug] : null;
            //
        } elseif (isset($is_network_wide, $type) && !isset($slug)) {
            return isset($this->apps_by_network_wide[(int) $is_network_wide][$type])
                ? $this->apps_by_network_wide[(int) $is_network_wide][$type] : [];
            //
        } elseif (isset($is_network_wide) && !isset($type) && !isset($slug)) {
            return $this->apps_by_network_wide[(int) $is_network_wide] ?? [];
            //
        } elseif (!isset($is_network_wide) && !isset($type) && !isset($slug)) {
            return $this->apps_by_network_wide;
        }
        throw $this->c::issue('Invalid parameters.');
    }
}
