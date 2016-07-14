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
            // NOTE: Avoid relying on utils in the shutdown phase.
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
        $theme_url      = $this->s::brandUrl('/changelog');
        $theme_slug     = $this->App->Config->©brand['§product_slug'];
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
        $plugin_url      = $this->s::brandUrl('/changelog');
        $plugin_slug     = $this->App->Config->©brand['§product_slug'];
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
     * On admin init.
     *
     * @since 160713 Update utils.
     */
    public function onAdminInit()
    {
        if ($this->App->Config->§specs['§in_wp']) {
            return $report; // Not applicable.
        } // i.e., Available inside WordPress already.
        // i.e., The project is hosted by WordPress.org.

        if ($this->App->Config->§specs['§type'] === 'plugin') {
            // This redirects the 'details' page for a plugin, to the product changelog
            // at the brand domain for the app; i.e., instead of pulling all details into WP.
            if (($_REQUEST['plugin'] ?? '') === $this->App->Config->©brand['§product_slug']
                && ($_REQUEST['tab'] ?? '') === 'plugin-information'
                && $this->s::isMenuPage('plugin-install.php')) {
                wp_redirect($this->s::brandUrl('/changelog')).exit();
            }
        }
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

        if (($api_url_for_latest_version = $this->apiUrlForLatestVersion())
            && !is_wp_error($remote_response = wp_remote_get($api_url_for_latest_version))
                && ($remote_api_response = $this->c::mbTrim((string) $remote_response['body']))
                && $this->c::isWsVersion($remote_api_response) // Avoid odd response body.
                && version_compare($remote_api_response, $version, '>=')) {
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

        if (($api_url_for_latest_package_via_license_key = $this->apiUrlForLatestPackageViaLicenseKey())
            && !is_wp_error($remote_response = wp_remote_get($api_url_for_latest_package_via_license_key))
            && is_object($remote_api_response = json_decode((string) $remote_response['body']))) {
            //
            if ($remote_api_response->success && $remote_api_response->data->url) {
                $package = $remote_api_response->data->url; // Latest available package.
                //
            } elseif (mb_strpos($remote_api_response->error->slug, 'notice::') === 0 && $remote_api_response->error->message) {
                $notice_heading = __('%1$s™ » \'%2$s\' license key error:', 'wp-sharks-core');
                $notice_heading = sprintf($notice_heading, esc_html($this->App::CORE_CONTAINER_NAME), esc_html($this->App->Config->©brand['§product_name']));
                $notice_markup  = $this->s::menuPageNoticeErrors($notice_heading, [$remote_api_response->error->message]);

                $this->s::enqueueNotice('', [
                    'id'   => '§license-key-error',
                    'type' => 'error',

                    'is_persistent'  => true,
                    'is_dismissable' => true,

                    'for_page' => $this->App->Config->§specs['§type'] === 'theme'
                        ? '/^(?:index|update\-core|themes)\.php$/ui'
                        : '/^(?:index|update\-core|plugins)\.php$/ui',

                    'markup' => $notice_markup,
                ]);
            }
        }
        $this->lastPackageCheck(['time' => time(), 'package' => $package]);

        return $package;
    }

    /**
     * API URL for latest version.
     *
     * @since 160530 Update utils.
     *
     * @return string URL (ready for GET request).
     */
    protected function apiUrlForLatestVersion(): string
    {
        $base       = $this->App->Config->©debug['©edge'] ? 'software/bleeding-edge' : 'software/latest';
        $uri        = '/'.$base.'/'.urlencode($this->App->Config->©brand['§product_slug']).'/version.txt';
        return $url = $this->s::coreBrandCdnUrl($uri);
    }

    /**
     * API URL for latest package (via license key).
     *
     * @since 160530 Update utils.
     *
     * @return string URL (ready for GET request).
     */
    protected function apiUrlForLatestPackageViaLicenseKey(): string
    {
        if (!($license_key = $this->s::getOption('§license_key'))) {
            return ''; // Not possible w/o license key.
        }
        $action_var = $this->s::coreBrandApiUrlArg('action');
        $data_var   = $this->s::coreBrandApiUrlArg('data');

        $args = [ // API call leading back to the core brand site.
            $action_var   => 'api-v1.0.get-product-download-url-via-license-key',
                $data_var => [
                    'license_key' => $license_key,
                    'site'        => site_url(),
                    'slug'        => $this->App->Config->©brand['§product_slug'],
                    'type'        => $this->App->Config->©debug['©edge'] ? 'bleeding-edge' : 'latest',
                ],
        ];
        return $this->c::addUrlQueryArgs($args, $this->s::coreBrandApiUrl());
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
        $default_empty_data = [
            'time'         => 0,
            'version'      => '',
            'product_slug' => '',
        ];
        if (isset($data)) { // Set automatically.
            $data['product_slug'] = $this->App->Config->©brand['§product_slug'];
        }
        $data = $this->s::sysOption('updater_last_version_check', $data);
        $data = is_array($data) ? $data : []; // Force array.

        $data = array_merge($default_empty_data, $data);
        $data = array_intersect_key($data, $default_empty_data);

        $data['time']         = (int) $data['time'];
        $data['version']      = (string) $data['version'];
        $data['product_slug'] = (string) $data['product_slug'];

        // NOTE: Must ensure product slug is a match.
        // e.g., If they started w/ lite and upgraded to pro.

        if (!$data['time'] || !$data['version'] || !$data['product_slug']
            || $data['product_slug'] !== $this->App->Config->©brand['§product_slug']) {
            $data = $default_empty_data; // Return all or none.
        }
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
        $default_empty_data = [
            'time'         => 0,
            'package'      => '',
            'product_slug' => '',
        ];
        if (isset($data)) { // Set automatically.
            $data['product_slug'] = $this->App->Config->©brand['§product_slug'];
        }
        $data = $this->s::sysOption('updater_last_package_check', $data);
        $data = is_array($data) ? $data : []; // Force array.

        $data = array_merge($default_empty_data, $data);
        $data = array_intersect_key($data, $default_empty_data);

        $data['time']         = (int) $data['time'];
        $data['package']      = (string) $data['package'];
        $data['product_slug'] = (string) $data['product_slug'];

        // NOTE: Must ensure product slug is a match.
        // e.g., If they started w/ lite and upgraded to pro.

        if (!$data['time'] || !$data['package'] || !$data['product_slug']
            || $data['product_slug'] !== $this->App->Config->©brand['§product_slug']) {
            $data = $default_empty_data; // Return all or none.
        }
        return $data;
    }
}
