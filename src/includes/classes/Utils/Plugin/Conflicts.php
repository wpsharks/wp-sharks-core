<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils\Plugin;

use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes as WCoreClasses;
use WebSharks\WpSharks\Core\Classes\Utils as WCoreUtils;
use WebSharks\WpSharks\Core\Interfaces as WCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as WCoreTraits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Plugin conflicts.
 *
 * @since 16xxxx WP notices.
 */
class Conflicts extends WCoreClasses\PluginBase
{
    /**
     * Conflicts?
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $conflicts = [];

    /**
     * Checked?
     *
     * @since 16xxxx
     *
     * @type bool Checked?
     */
    protected $checked = false;

    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct($Plugin);

        $this->check();
    }

    /**
     * Conflicts exist?
     *
     * @since 16xxxx Initial release.
     *
     * @return bool Conflicts exist?
     */
    public function exist(): bool
    {
        return !empty($this->conflicts);
    }

    /**
     * Check for conflicts.
     *
     * @since 16xxxx Initial release.
     */
    public function check()
    {
        if ($this->checked) {
            return; // Did this already.
        }
        $this->checked = true; // Checking now.

        if (!$this->Plugin->Config->conflicting['plugins']) {
            return; // Nothing to do here.
        }
        $active_plugins           = wc\all_active_plugins();
        $conflicting_plugin_slugs = array_keys($this->Plugin->Config->conflicting['plugins']);

        foreach ($active_plugins as $_active_plugin_basename) {
            $_active_plugin_slug = mb_strstr($_active_plugin_basename, '/', true);

            if ($_active_plugin_slug === $this->Plugin->Config->brand['slug']) {
                continue; // Sanity check. Cannot conflict w/ self.
            }
            if (in_array($_active_plugin_slug, $conflicting_plugin_slugs, true)) {
                if (in_array($_active_plugin_slug, $this->Plugin->Config->conflicting['deactivatble_plugins'], true)) {
                    add_action('admin_init', function () use ($_active_plugin_basename) {
                        if (function_exists('deactivate_plugins')) {
                            deactivate_plugins($_active_plugin_basename, true);
                        }
                    }, -1000);
                } else {
                    $_conflicting_plugin_name              = $this->Plugin->Config->conflicting['plugins'][$_active_plugin_slug];
                    $this->conflicts[$_active_plugin_slug] = $_conflicting_plugin_name; // `slug` => `name`.
                }
            }
        } // unset($_active_plugin_basename, $_active_plugin_slug, $_conflicting_plugin_name);

        $this->maybeEnqueueNotice(); // If conflicts exist.
    }

    /**
     * Maybe enqueue dashboard notice.
     *
     * @since 16xxxx Rewrite.
     */
    protected function maybeEnqueueNotice()
    {
        if (!$this->conflicts) {
            return; // No conflicts.
        }
        // @TODO enhance this w/ notice utils.

        add_action('all_admin_notices', function () {
            echo '<div class="error">'.// Error notice.
                 '   <p>'.// Running one or more conflicting plugins at the same time.
                 '      '.sprintf(__('<strong>%1$s</strong> is NOT running. A conflicting plugin, <strong>%2$s</strong>, is currently active. Please deactivate the %2$s plugin to clear this message.', SLUG_TD), esc_html($this_plugin_name), esc_html($conflicting_plugin_name)).
                 '   </p>'.
                 '</div>';
        });
    }
}
