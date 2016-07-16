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
 * App stats.
 *
 * @since 160713 App stats.
 */
class AppStats extends Classes\SCore\Base\Core
{
    /**
     * Outdated stats time.
     *
     * @since 160713 App stats.
     *
     * @type int Outdated stats time.
     */
    protected $outdated_stats_time;

    /**
     * Class constructor.
     *
     * @since 160713 App stats.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->outdated_stats_time = strtotime('-1 week');
        // No reason to post more than once per week, because our stats
        // database will not store it anyway. Must always be 1+ week apart.
    }

    /**
     * On admin init.
     *
     * @since 160713 App stats.
     */
    public function onAdminInit()
    {
        if (!in_array($this->App->Config->§specs['§type'], ['theme', 'plugin'], true)) {
            return; // Not applicable; themes/plugins only.
        } // e.g., We don't collect stats for a simple MU plugin.

        $last_posted = $this->lastPosted();

        if ($last_posted['time'] > $this->outdated_stats_time) {
            return; // Not time to post stats again yet.
        } // Must be 1+ week apart (enforced by remote API).

        $anonymous_stats = [
            'os'            => PHP_OS,
            'php_version'   => PHP_VERSION,
            'mysql_version' => $this->s::wpDb()->db_version(),

            'wp_version'      => WP_VERSION,
            'product_version' => $this->App::VERSION,
            'product'         => $this->App->Config->©brand['§product_slug'],
        ];
        wp_remote_post($this->s::coreBrandStatsUrl('/log'), [
                'blocking'  => false,
                'sslverify' => false,
                'body'      => $anonymous_stats,
        ]);
        $this->lastPosted(['time' => time()]); // Record last time.
    }

    /**
     * Last posted.
     *
     * @since 160713 App stats.
     *
     * @param array|null `[time]`.
     *
     * @return array Last posted data.
     */
    protected function lastPosted(array $data = null): array
    {
        $default_empty_data = [
            'time' => 0,
        ];
        $data = $this->s::sysOption('stats_last_posted', $data);
        $data = is_array($data) ? $data : []; // Force array.

        $data = array_merge($default_empty_data, $data);
        $data = array_intersect_key($data, $default_empty_data);

        $data['time'] = (int) $data['time'];

        return $data;
    }
}
