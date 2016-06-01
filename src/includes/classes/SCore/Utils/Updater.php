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

        if (!is_object($report)) { // Should not occur.
            debug(0, $this->c::issue(vars(), 'Unexpected report.'));
            return $report; // Not possible.
        }
        if (!isset($report->response) || !is_array($report->response)) {
            $report->response = []; // Force an array value.
            // This may not exist due to HTTP errors or other quirks.
        }
        $theme_url      = $this->s::brandUrl();
        $theme_slug     = $this->App->Config->©brand['©slug'];
        $latest_version = $this->latestVersion(); // Or last known.

        if (version_compare($latest_version, $this->App::VERSION, '>')) {
            if (($latest_package = $this->latestPackage($latest_version))) {
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

        if (!is_object($report)) { // Should not occur.
            debug(0, $this->c::issue(vars(), 'Unexpected report.'));
            return $report; // Not possible.
        }
        if (!isset($report->response) || !is_array($report->response)) {
            $report->response = []; // Force an array value.
            // This may not exist due to HTTP errors or other quirks.
        }
        $plugin_url      = $this->s::brandUrl();
        $plugin_slug     = $this->App->Config->©brand['©slug'];
        $plugin_basename = plugin_basename($this->App->Config->§specs['§file']);
        $latest_version  = $this->latestVersion(); // Or last known.

        if (version_compare($latest_version, $this->App::VERSION, '>')) {
            if (($latest_package = $this->latestPackage($latest_version))) {
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
            if ($last_check['version'] // Use the last-check version?
                    && version_compare($last_check['version'], $this->App::VERSION, '>=')) {
                $version = $last_check['version'];
            }
            return $version; // Last check or latest version.
        } // Already checked recently. Don't do it again (yet).

        if (!is_wp_error($response = wp_remote_get($this->latestVersionUrl()))
                && ($remote_version = $this->c::mbTrim((string) $response['body']))
                && $this->c::isWsVersion($remote_version) // Avoid odd body.
                && version_compare($remote_version, $this->App::VERSION, '>=')
            ) {
            $version = $remote_version; // Latest available version.
        }
        $this->lastVersionCheck(['time' => time(), 'version' => $version]);

        return $version;
    }

    /**
     * Latest package.
     *
     * @since 160530 Update utils.
     *
     * @param string $version Package version.
     *
     * @return string Latest package matching `$version`.
     */
    protected function latestPackage(string $version): string
    {
        $last_check = $this->lastPackageCheck();
        $package    = ''; // No fallback possible.

        if ($last_check['time'] > $this->outdated_check_time) {
            if ($version === $last_check['version'] && $last_check['package']) {
                $package = $last_check['package']; // Matching version.
            }
            return $package; // Last check or latest package.
        } // Already checked recently. Don't do it again (yet).

        if (!is_wp_error($response = wp_remote_get($this->latestPackageUrl($version)))
                && ($remote_package = $this->c::mbTrim((string) $response['body']))
                && preg_match('/^http/ui', $remote_package)) { // Avoid odd body.
            $package = $remote_package; // Latest available package.
        }
        $this->lastPackageCheck(['time' => time(), 'version' => $version, 'package' => $package]);

        return $version;
    }

    /**
     * Latest version URL.
     *
     * @since 160530 Update utils.
     *
     * @return string Latest version URL.
     */
    protected function latestVersionUrl(): string
    {
        if ($this->App->Config->©debug['©edge']) {
            return 'https://cdn.wpsharks.com/software/bleeding-edge/'.urlencode($this->App->Config->©brand['©slug']).'/version.txt';
        } else {
            return 'https://cdn.wpsharks.com/software/latest/'.urlencode($this->App->Config->©brand['©slug']).'/version.txt';
        }
    }

    /**
     * Latest package URL.
     *
     * @since 160530 Update utils.
     *
     * @return string Latest package URL.
     */
    protected function latestPackageUrl(string $version): string
    {
        // @TODO Once an API is in place at wpsharks.com.
        // The API should accept `license_key`, `slug`, `version`, and `edge=0|1`.
        // The API should return a transient URL leading to a ZIP file via redirection.
        // The transient URL should be allowed to live for no less than 15 minutes.

        // @TODO Also clear OPcache on any update.

        if ($this->App->Config->©debug['©edge']) {
            return 'https://cdn.wpsharks.com/software/bleeding-edge/'.urlencode($this->App->Config->©brand['©slug']).'/version.txt';
        } else {
            return 'https://cdn.wpsharks.com/software/latest/'.urlencode($this->App->Config->©brand['©slug']).'/version.txt';
        }
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
     * @param array|null `[time,version,package]`.
     *
     * @return array Last package-check data.
     */
    protected function lastPackageCheck(array $data = null): array
    {
        $default_data = [
            'time'    => 0,
            'version' => '',
            'package' => '',
        ];
        $data = $this->s::sysOption('updater_last_package_check', $data);
        $data = is_array($data) ? $data : []; // Force array.

        $data = array_merge($default_data, $data);
        $data = array_intersect_key($data, $default_data);

        $data['time']    = (int) $data['time'];
        $data['version'] = (string) $data['version'];
        $data['package'] = (string) $data['package'];

        return $data;
    }
}
