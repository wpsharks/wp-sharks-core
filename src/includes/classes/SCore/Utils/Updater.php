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
 * Update utils.
 *
 * @since 160530 Update utils.
 */
class Updater extends Classes\SCore\Base\Core
{
    /**
     * Outdated check time.
     *
     * @since 160530 Update utils.
     *
     * @type int Outdated check time.
     */
    protected $outdated_check_time;

    /**
     * Class constructor.
     *
     * @since 160530 Update utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->outdated_check_time = strtotime('-15 minutes');
    }

    /**
     * Flush the OPcache.
     *
     * @since 160620 Update utils.
     */
    public function onUpgraderProcessComplete()
    {
        add_action('shutdown', function () {
            // NOTE: Avoid relying on objects in the shutdown phase.
            // PHP's shutdown phase is known to destruct objects randomly.
            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }
        });
    }

    /**
     * Transient filter.
     *
     * @since 160530 Update utils.
     *
     * @param \StdClass|mixed $report Report details.
     *
     * @return \StdClass|mixed Report details.
     */
    public function onGetSiteTransientUpdateThemes($report)
    {
        if ($this->App->Config->§specs['§in_wp']) {
            return $report; // Not applicable.
        } // i.e., Available inside WordPress already.
        // i.e., The project is hosted by WordPress.org.

        if ($this->App->Config->§specs['§type'] !== 'theme') {
            return $report; // Not applicable.
        } // i.e., Applies only to theme instances.

        if (!is_object($report)) { // e.g., Does not exist yet?
            $report = new \StdClass(); // Force object instance.
        }
        if (!isset($report->response) || !is_array($report->response)) {
            $report->response = []; // Force an array value.
            // This may not exist due to HTTP errors or other quirks.
        }
        $theme_url      = $this->s::brandUrl();
        $theme_slug     = $this->App->Config->©brand['©slug'];
        $latest_version = $this->latestVersion(); // Latest available.

        if (version_compare($latest_version, $this->App::VERSION, '>')) {
            if (($latest_package = $this->latestPackage())) {
                $report->response[$theme_slug] = (object) [
                    'theme'       => $theme_slug,
                    'url'         => $theme_url,
                    'new_version' => $latest_version,
                    'package'     => $theme_package,
                ];
            } // ↑ If we can acquire latest package also.
        }
        return $report; // With possible update for this app.
    }

    /**
     * Transient filter.
     *
     * @since 160530 Update utils.
     *
     * @param \StdClass|mixed $report Report details.
     *
     * @return \StdClass|mixed Report details.
     */
    public function onGetSiteTransientUpdatePlugins($report)
    {
        if ($this->App->Config->§specs['§in_wp']) {
            return $report; // Not applicable.
        } // i.e., Available inside WordPress already.
        // i.e., The project is hosted by WordPress.org.

        if ($this->App->Config->§specs['§type'] !== 'plugin') {
            return $report; // Not applicable.
        } // i.e., Applies only to plugin instances.

        if (!is_object($report)) { // e.g., Does not exist yet?
            $report = new \StdClass(); // Force object instance.
        }
        if (!isset($report->response) || !is_array($report->response)) {
            $report->response = []; // Force an array value.
            // This may not exist due to HTTP errors or other quirks.
        }
        $plugin_url      = $this->s::brandUrl();
        $plugin_slug     = $this->App->Config->©brand['©slug'];
        $plugin_basename = plugin_basename($this->App->Config->§specs['§file']);
        $latest_version  = $this->latestVersion(); // Latest available.

        if (version_compare($latest_version, $this->App::VERSION, '>')) {
            if (($latest_package = $this->latestPackage())) {
                $report->response[$plugin_basename] = (object) [
                    'id'          => -1,
                    'slug'        => $plugin_slug,
                    'plugin'      => $plugin_basename,
                    'url'         => $plugin_url,
                    'new_version' => $latest_version,
                    'package'     => $latest_package,
                ];
            } // ↑ If we can acquire latest package also.
        }
        return $report; // With possible update for this app.
    }

    /**
     * Latest version.
     *
     * @since 160530 Update utils.
     *
     * @return string Latest version.
     */
    protected function latestVersion(): string
    {
        $last_check = $this->lastVersionCheck();
        $version    = $this->App::VERSION; // Fallback.

        if ($last_check['time'] > $this->outdated_check_time) {
            return $last_check['version']; // According to last check.
        } // Already checked this recently. Don't do it again right now.

        if (($get_latest_version_url = $this->getLatestVersionUrl())
            && !is_wp_error($remote_response = wp_remote_get($get_latest_version_url))
                && ($remote_api_response = $this->c::mbTrim((string) $remote_response['body']))
                && $this->c::isWsVersion($remote_api_response) // Avoid odd response body.
                && version_compare($remote_api_response, $this->App::VERSION, '>=')) {
            $version = $remote_api_response; // Latest available version.
        }
        $this->lastVersionCheck(['time' => time(), 'version' => $version]);

        return $version;
    }

    /**
     * Latest package.
     *
     * @since 160530 Update utils.
     *
     * @return string Latest package.
     */
    protected function latestPackage(): string
    {
        $last_check = $this->lastPackageCheck();
        $package    = ''; // No fallback possible.

        if ($last_check['time'] > $this->outdated_check_time) {
            return $last_check['package']; // According to last check.
        } // Already checked this recently. Don't do it again right now.

        if (($get_latest_package_url = $this->getLatestPackageUrl())
            && !is_wp_error($remote_response = wp_remote_get($get_latest_package_url))
                && is_object($remote_api_response = json_decode((string) $remote_response['body']))
                && !empty($remote_api_response->success) && !empty($remote_api_response->data->url)) {
            $package = $remote_api_response->data->url; // Latest available package.
        }
        $this->lastPackageCheck(['time' => time(), 'package' => $package]);

        return $package;
    }

    /**
     * Latest version URL.
     *
     * @since 160530 Update utils.
     *
     * @return string URL (ready for GET request).
     */
    protected function getLatestVersionUrl(): string
    {
        $url = 'https://cdn.wpsharks.com/software';
        $url .= $this->App->Config->©debug['©edge'] ? '/bleeding-edge' : '/latest';
        $url .= '/'.urlencode($this->App->Config->©brand['©slug']);
        return $url .= '/version.txt';
    }

    /**
     * Latest package URL.
     *
     * @since 160530 Update utils.
     *
     * @return string URL (ready for GET request).
     */
    protected function getLatestPackageUrl(): string
    {
        $license_key = $this->s::getOption('§license_key');

        if ($this->App->Config->§specs['§is_pro'] && !$license_key) {
            return ''; // Not possible w/o license key.
        }
        $args = [
            'wps_action'      => 'get-product-package-url...via-api',
            'wps_action_data' => [
                'api_version' => '1.0',
                'product'     => [
                    'license_key' => $license_key,
                    'slug'        => $this->App->Config->©brand['©slug'],
                ],
                'type' => $this->App->Config->©debug['©edge'] ? 'bleeding-edge' : 'latest',
            ],
        ];
        return $this->c::addUrlQueryArgs($args, 'https://api.wpsharks.com/');
    }

    /**
     * Last version-check data.
     *
     * @since 160530 Update utils.
     *
     * @param array|null `[time,version]`.
     *
     * @return array Last version-check data.
     */
    protected function lastVersionCheck(array $data = null): array
    {
        $default_data = [
            'time'    => 0,
            'version' => '',
        ];
        $data = $this->s::sysOption('updater_last_version_check', $data);
        $data = is_array($data) ? $data : []; // Force array.

        $data = array_merge($default_data, $data);
        $data = array_intersect_key($data, $default_data);

        $data['time']    = (int) $data['time'];
        $data['version'] = (string) $data['version'];

        return $data;
    }

    /**
     * Last package-check data.
     *
     * @since 160530 Update utils.
     *
     * @param array|null `[time,package]`.
     *
     * @return array Last package-check data.
     */
    protected function lastPackageCheck(array $data = null): array
    {
        $default_data = [
            'time'    => 0,
            'package' => '',
        ];
        $data = $this->s::sysOption('updater_last_package_check', $data);
        $data = is_array($data) ? $data : []; // Force array.

        $data = array_merge($default_data, $data);
        $data = array_intersect_key($data, $default_data);

        $data['time']    = (int) $data['time'];
        $data['package'] = (string) $data['package'];

        return $data;
    }
}
