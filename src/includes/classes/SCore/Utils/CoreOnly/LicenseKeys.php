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
 * License key utils.
 *
 * @since 160710 License key utils.
 */
class LicenseKeys extends Classes\SCore\Base\Core
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
     * Request license key via dashboard notice.
     *
     * @since 160710 License key utils.
     *
     * @param string $app_slug App slug.
     */
    public function maybeRequestViaNotice(string $app_slug)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return; // Not possible.
        } elseif ($App->Config->§specs['§in_wp']) {
            return; // Not necessary.
        } elseif ($App->s::getOption('§license_key')) {
            return; // Already have a license key.
        }
        $App->s::enqueueNotice('', [
            'id'   => '§license-key',
            'type' => 'info',

            'is_persistent'  => true,
            'is_dismissable' => true,

            'is_applicable' => function (Classes\App $App) {
                return $App->Parent->s::licenseKeyRequestViaNoticeIsApplicable($App->Config->©brand['©slug']);
            },
            'markup' => function (Classes\App $App) {
                return $App->Parent->s::licenseKeyRequestViaNoticeMarkup($App->Config->©brand['©slug']);
            },
        ]);
    }

    /**
     * Request via notice is applicable?
     *
     * @since 160712 License key utils.
     *
     * @param string $app_slug App slug.
     *
     * @return null|bool Null = dequeue entirely.
     */
    public function requestViaNoticeIsApplicable(string $app_slug)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return null; // Dequeue entirely.
        } elseif ($App->s::getOption('§license_key')) {
            return null; // Dequeue entirely.
        } elseif ($this->s::isOwnMenuPage()) {
            return false; // Not on core pages.
        } elseif (in_array($menu_page = $this->s::currentMenuPage(), ['update-core.php'], true)) {
            return false; // Not during core update.
        } elseif (in_array($menu_page, ['themes.php', 'plugins.php', 'update.php'], true) && !empty($_REQUEST['action'])) {
            return false; // Not during a plugin install/activate/update.
        }
        return true; // Is applicable; i.e., do display.
    }

    /**
     * Request via notice markup.
     *
     * @since 160712 License key utils.
     *
     * @param string $app_slug App slug.
     *
     * @return string Empty = dequeue entirely.
     */
    public function requestViaNoticeMarkup(string $app_slug): string
    {
        if (($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return $App->c::getTemplate('s-core/notices/license-key.php')->parse();
        }
        return ''; // Dequeue entirely.
    }

    /**
     * License key update handler.
     *
     * @since 160710 License key utils.
     */
    public function onRestActionUpdateLicenseKeys()
    {
        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden(); // Not allowed!
        }
        $apps_by_slug = $this->s::getAppsBySlug();
        $data         = (array) $this->s::restActionData(true);
        $license_keys = (array) ($data['license_keys'] ?? []);
        $Errors       = $this->c::error();

        foreach ($license_keys as $_app_slug => $_license_key) {
            if (!$_app_slug || !is_string($_app_slug) || !is_string($_license_key)) {
                continue; // Bypass; invalid data.
            } elseif (!($_App = $apps_by_slug[$_app_slug] ?? null)) {
                continue; // App no longer active.
            }
            if ($_license_key) { // Activate (or reactivate) a license key.
                if ($this->c::isError($_Error = $this->activate($_app_slug, $_license_key))) {
                    $Errors->add($_Error->slug(), '**'.$_App->Config->©brand['©name'].':** '.$_Error->message());
                } else {
                    $_App->s::updateOptions(['§license_key' => $_license_key]);
                }
            } elseif (!$_license_key && $_App->Config->§options['§license_key']) { // Deactivate.
                if ($this->c::isError($_Error = $this->deactivate($_app_slug, $_App->Config->§options['§license_key']))) {
                    $Errors->add($_Error->slug(), '**'.$_App->Config->©brand['©name'].':** '.$_Error->message());
                } else {
                    $_App->s::updateOptions(['§license_key' => '']);
                }
            }
        } // unset($_app_slug, $_license_key, $_App, $_Error); // Housekeeping.

        if ($Errors->exist()) {
            $notice_heading = sprintf(__('The following errors occurred while updating license keys:', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME));
            $notice_markup  = $this->s::menuPageNoticeErrors($notice_heading, $Errors->messages());
            $this->s::enqueueUserNotice($notice_markup, ['type' => 'error']);
        } else {
            $notice_markup = sprintf(__('%1$s™ license keys updated successfully.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME));
            $this->s::enqueueUserNotice($notice_markup, ['type' => 'success']);
        }
        wp_redirect($this->s::menuPageUrl()).exit(); // Stop on redirection.
    }

    /**
     * Activate a license key.
     *
     * @since 160710 License key utils.
     *
     * @param string $app_slug    App slug.
     * @param string $license_key License key.
     *
     * @return bool|Error True on success, error on failure.
     */
    public function activate(string $app_slug, string $license_key)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return $this->c::error('missing-app');
        }
        $remote_post_url  = $this->s::coreBrandApiUrl();
        $remote_post_body = [ // API call leading back to core brand.
            $this->s::coreBrandApiUrlArg('action') => 'api-v1.0.activate-product-license-key',
            $this->s::coreBrandApiUrlArg('data')   => [
                'license_key' => $license_key,
                'site'        => site_url(),
                'slug'        => $App->Config->©brand['©slug'],
            ],
        ];
        $remote_response     = wp_remote_post($remote_post_url, ['body' => $remote_post_body]);
        $remote_api_response = is_wp_error($remote_response) ? null : json_decode($remote_response['body']);

        if (is_wp_error($remote_response)) {
            return $this->s::wpErrorConvert($remote_response);
        } elseif (!is_object($remote_api_response)) {
            return $this->c::error('non-object-response', __('Unknown error. Please wait 5 minutes & try again.', 'wp-sharks-core'));
        } elseif (!$remote_api_response->success) {
            return $this->c::error($remote_api_response->error->slug, $remote_api_response->error->message);
        }
        return true; // Success; i.e., no problems.
    }

    /**
     * Dectivate a license key.
     *
     * @since 160710 License key utils.
     *
     * @param string $app_slug    App slug.
     * @param string $license_key License key.
     *
     * @return bool|Error True on success, error on failure.
     */
    public function deactivate(string $app_slug, string $license_key)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return $this->c::error('missing-app');
        }
        $remote_post_url  = $this->s::coreBrandApiUrl();
        $remote_post_body = [ // API call leading back to core brand.
            $this->s::coreBrandApiUrlArg('action') => 'api-v1.0.deactivate-product-license-key',
            $this->s::coreBrandApiUrlArg('data')   => [
                'license_key' => $license_key,
                'site'        => site_url(),
                'slug'        => $App->Config->©brand['©slug'],
            ],
        ];
        $remote_response     = wp_remote_post($remote_post_url, ['body' => $remote_post_body]);
        $remote_api_response = is_wp_error($remote_response) ? null : json_decode($remote_response['body']);

        if (is_wp_error($remote_response)) {
            return $this->s::wpErrorConvert($remote_response);
        } elseif (!is_object($remote_api_response)) {
            return $this->c::error('non-object-response', __('Unknown error. Please wait 5 minutes & try again.', 'wp-sharks-core'));
        } elseif (!$remote_api_response->success) {
            return $this->c::error($remote_api_response->error->slug, $remote_api_response->error->message);
        }
        return true; // Success; i.e., no problems.
    }
}
