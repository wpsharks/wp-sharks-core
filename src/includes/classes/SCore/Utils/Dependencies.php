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

/**
 * Dependencies.
 *
 * @since 16xxxx Dependencies.
 */
class Dependencies extends Classes\SCore\Base\Core
{
    /**
     * Checked?
     *
     * @since 16xxxx
     *
     * @type bool Checked?
     */
    protected $checked;

    /**
     * Outstanding plugins.
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $plugins;

    /**
     * Outstanding themes.
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $themes;

    /**
     * Class constructor.
     *
     * @since 16xxxx Dependencies.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->plugins = [];
        $this->themes  = [];
        $this->checked = false;

        $this->check(); // On instantiation.
    }

    /**
     * Dependendies outstanding?
     *
     * @since 16xxxx Dependencies.
     *
     * @return bool Dependendies outstanding?
     */
    public function outstanding(): bool
    {
        return $this->plugins || $this->themes;
    }

    /**
     * Check for dependencies.
     *
     * @since 16xxxx Dependencies.
     */
    protected function check()
    {
        if ($this->checked) {
            return; // Done.
        }
        $this->checked = true;

        if (!$this->App->Config->§dependencies['§plugins']
            && !$this->App->Config->§dependencies['§themes']) {
            return; // Nothing to do here.
        }
        $all_active_plugin_slugs = $this->s::allActivePlugins();
        $all_active_theme_slugs  = array_unique([get_template(), get_stylesheet()]);

        $plugin_dependencies = $this->App->Config->§dependencies['§plugins'];
        $theme_dependencies  = $this->App->Config->§dependencies['§themes'];

        foreach (['plugin' => 'Plugin', 'theme' => 'Theme'] as $_type => $_ucf_type) {
            foreach (${$_type.'_dependencies'} as $_dependency_slug => $_dependency_args) {
                if ($_dependency_slug === $this->App->base_dir_basename && $this->App->Config->§specs['§type'] === $_type) {
                    continue; // Sanity check. Cannot depend on self of the same type.
                }
                if (!in_array($_dependency_slug, ${'all_active_'.$_type.'_slugs'}, true)) {
                    $this->{$_type.'s'}['inactive'][$_dependency_slug] = ['args' => $_dependency_args];

                // Else we can run a more in-depth test if the app provides a callback.
                } elseif (!empty($_dependency_args['test']) && is_callable($_dependency_args['test'])) {
                    if (($_test_result = $_dependency_args['test']($_dependency_slug)) !== true && !empty($_test_result['reason'])) {
                        $this->{$_type.'s'}[$_test_result['reason']][$_dependency_slug] = ['args' => $_dependency_args, 'test_result' => $_test_result];
                    }
                }
            } // unset($_dependency_slug, $_dependency_args, $_test_result); // Housekeeping.
        } // unset($_type, $_ucf_type); // Housekeeping.

        $this->maybeNotify(); // If dependencies are outstanding.
    }

    /**
     * Maybe enqueue dashboard notice.
     *
     * @since 16xxxx Dependencies.
     */
    protected function maybeNotify()
    {
        if (!is_admin()) {
            return; // Not applicable.
        } elseif (!$this->outstanding()) {
            return; // No conflicts.
        }
        foreach (['plugin' => 'Plugin', 'theme' => 'Theme'] as $_type => $_ucf_type) {
            // Check `inactive` and/or `missing` (same thing in this context).
            foreach ($this->{$_type.'s'}['inactive'] ?? [] as $_dependency_slug => $_dependency_data) {
                if ($this->s::{$_type.'IsInstalled'}($_dependency_slug)) {
                    $this->maybeEnqueueActivationNotice(array_merge($_dependency_data['args'], [
                        'type' => $_type,
                        'slug' => $_dependency_slug,
                    ]));
                } else { // It's not even installed.
                    $this->maybeEnqueueInstallationNotice(array_merge($_dependency_data['args'], [
                        'type' => $_type,
                        'slug' => $_dependency_slug,
                    ]));
                }
                return; // Deal with one dependency at a time.
            } // unset($_dependency_slug, $_dependency_data); // Housekeeping.

            // Check `needs-upgrade`; i.e., current version is not adequate.
            foreach ($this->{$_type.'s'}['needs-upgrade'] ?? [] as $_dependency_slug => $_dependency_data) {
                if (!empty($_dependency_data['test_result']['min_version'])) {
                    $this->maybeEnqueueUpgradeNotice(array_merge($_dependency_data['args'], [
                        'type'        => $_type,
                        'slug'        => $_dependency_slug,
                        'min_version' => $_dependency_data['test_result']['min_version'],
                    ]));
                } else { // The test did not produce the expected result data.
                    throw new Exception(sprintf('Missing `min_version` from test against: `%1$s`.', $_dependency_slug));
                }
                return; // Deal with one dependency at a time.
            } // unset($_dependency_slug, $_dependency_data); // Housekeeping.

            // Check `needs-downgrade`; i.e., current version is not adequate.
            foreach ($this->{$_type.'s'}['needs-downgrade'] ?? [] as $_dependency_slug => $_dependency_data) {
                if (!empty($_dependency_data['test_result']['max_version'])) {
                    $this->maybeEnqueueDowngradeNotice(array_merge($_dependency_data['args'], [
                        'type'        => $_type,
                        'slug'        => $_dependency_slug,
                        'max_version' => $_dependency_data['test_result']['max_version'],
                    ]));
                } else { // The test did not produce the expected result data.
                    throw new Exception(sprintf('Missing `max_version` from test against: `%1$s`.', $_dependency_slug));
                }
                return; // Deal with one dependency at a time.
            } // unset($_dependency_slug, $_dependency_data); // Housekeeping.
        } // unset($_type, $_ucf_type); // Housekeeping.
    }

    /**
     * URL to install dependency.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param string $slug Dependency slug.
     * @param string $type Dependency type.
     *
     * @return string URL to install dependency.
     */
    protected function installUrl(string $slug, string $type): string
    {
        $qualifier           = $slug; // Same as slug.
        $identifier          = $type; // Same as type.
        $nonce_action_prefix = 'install'; // Same for both.

        $args = [
            'action'     => 'install-'.$type,
            'action_via' => $this->App->Config->©brand['©slug'],
            $identifier  => $qualifier, // Dependency.
        ];
        $admin_url = is_multisite() ? 'network_admin_url' : 'self_admin_url';

        $url = $admin_url('/update.php');
        $url = $this->c::addUrlQueryArgs($args, $url);
        $url = $this->s::addUrlNonce($url, $nonce_action_prefix.'-'.$type.'_'.$qualifier);

        return $url;
    }

    /**
     * URL to activate dependency.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param string $slug Dependency slug.
     * @param string $type Dependency type.
     *
     * @return string URL to activate dependency; else empty string.
     */
    protected function activateUrl(string $slug, string $type): string
    {
        $qualifier           = $type === 'theme' ? $slug : null;
        $identifier          = $type === 'theme' ? 'stylesheet' : 'plugin';
        $nonce_action_prefix = $type === 'theme' ? 'switch' : 'activate';

        if ($type === 'plugin' && !($qualifier = $this->s::installedPluginData($slug, 'basename'))) {
            return ''; // Not installed or no basename.
        } // Possible empty string on failure. Please check return value.

        $args = [
            'action'     => 'activate',
            'action_via' => $this->App->Config->©brand['©slug'],
            $identifier  => $qualifier, // Dependency.
        ];
        // Activations always use `self_admin_url()`.

        $url = self_admin_url('/'.$type.'s.php');
        $url = $this->c::addUrlQueryArgs($args, $url);
        $url = $this->s::addUrlNonce($url, $nonce_action_prefix.'-'.$type.'_'.$qualifier);

        return $url;
    }

    /**
     * URL to upgrade dependency.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param string $slug Dependency slug.
     * @param string $type Dependency type.
     *
     * @return string URL to upgrade dependency; else empty string.
     */
    protected function upgradeUrl(string $slug, string $type): string
    {
        $identifier          = $type; // Same as type.
        $qualifier           = $type === 'theme' ? $slug : null;
        $nonce_action_prefix = 'upgrade'; // Same for both.

        if ($type === 'plugin' && !($qualifier = $this->s::installedPluginData($slug, 'basename'))) {
            return ''; // Not installed or no basename.
        } // Possible empty string on failure. Please check return value.

        $args = [
            'action'     => 'upgrade-'.$type,
            'action_via' => $this->App->Config->©brand['©slug'],
            $identifier  => $qualifier, // Dependency.
        ];
        $admin_url = is_multisite() ? 'network_admin_url' : 'self_admin_url';

        $url = $admin_url('/update.php');
        $url = $this->c::addUrlQueryArgs($args, $url);
        $url = $this->s::addUrlNonce($url, $nonce_action_prefix.'-'.$type.'_'.$qualifier);

        return $url;
    }

    /**
     * URL to dependency archive.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param string $slug    Dependency slug.
     * @param string $type    Dependency type.
     * @param string $version Specific version.
     *
     * @return string URL to archive (or direct download if `$version` is given).
     */
    protected function archiveUrl(string $slug, string $type, string $version = ''): string
    {
        switch ($type) { // Based on dependency type.
            case 'plugin':
                if ($version) {
                    return 'https://downloads.wordpress.org/plugin/'.urlencode($slug).'.'.urlencode($version).'.zip';
                }
                return 'https://wordpress.org/plugins/'.urlencode($slug).'/developers/';

            case 'theme':
                if ($version) {
                    return 'https://downloads.wordpress.org/theme/'.urlencode($slug).'.'.urlencode($version).'.zip';
                }
                return 'https://themes.svn.wordpress.org/'.urlencode($slug).'/';

            default: // Default case handler.
                throw new Exception('Unexpected type.');
        }
    }

    /**
     * Maybe enqueue dependency installation notice.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param array $args Required dependency args.
     *
     * @note Intentionally choosing not to use built-in notice utilities here.
     *  The notice utilities set option values, and if we have outstanding dependencies
     *  (e.g., something triggered by a plugin hook) that could lead to unforeseen problems.
     *
     * @note Not only that, but the hooks needed to use notice utilities are not attached
     * until after a check for dependencies has been finalized; i.e., notice utils won't work anyway.
     */
    protected function maybeEnqueueInstallationNotice(array $args)
    {
        if (!is_admin()) {
            return; // Not applicable.
        }
        $default_args = [
            'type'  => '',
            'slug'  => '',
            'name'  => '',
            'url'   => '',
            'in_wp' => null,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['type']  = (string) $args['type'];
        $args['slug']  = (string) $args['slug'];
        $args['name']  = (string) $args['name'];
        $args['url']   = (string) $args['url'];
        $args['in_wp'] = (bool) $args['in_wp'];

        foreach ($args as $_arg => $_value) {
            if (is_string($_value) && !isset($_value[0])) {
                throw new Exception(sprintf('Missing argument: `%1$s`.', $_arg));
            }
        } // unset($_arg, $_value); // Housekeeping.

        if ($args['in_wp']) { // It's in WordPress?
            if (!($dep_install_url = $this->installUrl($args['slug'], $args['type']))) {
                throw new Exception(sprintf('Unable to generate install URL for: `%1$s`.', $args['slug']));
            }
            $markup = $this->bubble; // Initialize markup w/ the bubble icon.
            $markup .= sprintf(__('<strong>%1$s is not active.</strong> It depends on the <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> %4$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['type'])).'<br />';
            $markup .= $this->arrow.' '.sprintf(__('A simple addition is necessary. <strong><a href="%1$s">Click here to install the \'%2$s\' dependency</a></strong>.', 'wp-sharks-core'), esc_url($dep_install_url), esc_html($args['name'])).'<br />';
            $markup .= sprintf(__('<em>To remove this message install the dependency or remove %1$s from WordPress.</em>', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']));
        } else {
            $markup = $this->bubble; // Initialize markup w/ the bubble icon.
            $markup .= sprintf(__('<strong>%1$s is not active.</strong> It depends on the <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> %4$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['type'])).'<br />';
            $markup .= $this->arrow.' '.sprintf(__('An addition is necessary. <strong><a href="%1$s" target="_blank">Click here to get the \'%2$s\' dependency</a></strong>.', 'wp-sharks-core'), esc_url($args['url']), esc_html($args['name'])).'<br />';
            $markup .= sprintf(__('<em>To remove this message install the dependency or remove %1$s from WordPress.</em>', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']));
        }
        add_action('all_admin_notices', function () use ($args, $markup) {
            global $pagenow; // Needed below.

            if (!current_user_can('install_'.$args['type'].'s')) {
                return; // Do not show.
            }
            if (in_array($pagenow, ['plugins.php', 'themes.php', 'update.php'], true)
                && ($_REQUEST['action_via'] ?? '') === $this->App->Config->©brand['©slug']) {
                return; // Not during a plugin install/activate/update action.
            }
            echo '<div class="notice notice-warning">'.
                    '<p>'.$markup.'</p>'.
                 '</div>';
        });
    }

    /**
     * Maybe enqueue dependency activation notice.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param array $args Required dependency args.
     *
     * @note Intentionally choosing not to use built-in notice utilities here.
     *  The notice utilities set option values, and if we have outstanding dependencies
     *  (e.g., something triggered by a plugin hook) that could lead to unforeseen problems.
     *
     * @note Not only that, but the hooks needed to use notice utilities are not attached
     * until after a check for dependencies has been finalized; i.e., notice utils won't work anyway.
     */
    protected function maybeEnqueueActivationNotice(array $args)
    {
        if (!is_admin()) {
            return; // Not applicable.
        }
        $default_args = [
            'type'  => '',
            'slug'  => '',
            'name'  => '',
            'url'   => '',
            'in_wp' => null,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['type']  = (string) $args['type'];
        $args['slug']  = (string) $args['slug'];
        $args['name']  = (string) $args['name'];
        $args['url']   = (string) $args['url'];
        $args['in_wp'] = (bool) $args['in_wp'];

        foreach ($args as $_arg => $_value) {
            if (is_string($_value) && !isset($_value[0])) {
                throw new Exception(sprintf('Missing argument: `%1$s`.', $_arg));
            }
        } // unset($_arg, $_value); // Housekeeping.

        // If we are activating the dependency, it IS in WordPress.
        if (!($dep_activate_url = $this->activateUrl($args['slug'], $args['type']))) {
            throw new Exception(sprintf('Unable to generate activation URL for: `%1$s`.', $args['slug']));
        }
        $markup = $this->bubble; // Initialize markup w/ the bubble icon.
        $markup .= sprintf(__('<strong>%1$s is not active.</strong> It depends on the <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> %4$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['type'])).'<br />';
        $markup .= $this->arrow.' '.sprintf(__('A simple activation is necessary. <strong><a href="%1$s">Click here to activate the \'%2$s\' dependency</a></strong>.', 'wp-sharks-core'), esc_url($dep_activate_url), esc_html($args['name'])).'<br />';
        $markup .= sprintf(__('<em>To remove this message activate the dependency or deactivate %1$s until you do.</em>', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']));

        add_action('all_admin_notices', function () use ($args, $markup) {
            global $pagenow; // Needed below.

            if (!current_user_can(($args['type'] === 'theme' ? 'switch' : 'activate').'_'.$args['type'].'s')) {
                return; // Do not show.
            }
            if (in_array($pagenow, ['plugins.php', 'themes.php', 'update.php'], true)
                && ($_REQUEST['action_via'] ?? '') === $this->App->Config->©brand['©slug']) {
                return; // Not during a plugin install/activate/update action.
            }
            echo '<div class="notice notice-warning">'.
                    '<p>'.$markup.'</p>'.
                 '</div>';
        });
    }

    /**
     * Maybe enqueue dependency upgrade notice.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param array $args Required dependency args.
     *
     * @note Intentionally choosing not to use built-in notice utilities here.
     *  The notice utilities set option values, and if we have outstanding dependencies
     *  (e.g., something triggered by a plugin hook) that could lead to unforeseen problems.
     *
     * @note Not only that, but the hooks needed to use notice utilities are not attached
     * until after a check for dependencies has been finalized; i.e., notice utils won't work anyway.
     */
    protected function maybeEnqueueUpgradeNotice(array $args)
    {
        if (!is_admin()) {
            return; // Not applicable.
        }
        $default_args = [
            'type'        => '',
            'slug'        => '',
            'name'        => '',
            'url'         => '',
            'in_wp'       => null,
            'min_version' => '',
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['type']        = (string) $args['type'];
        $args['slug']        = (string) $args['slug'];
        $args['name']        = (string) $args['name'];
        $args['url']         = (string) $args['url'];
        $args['in_wp']       = (bool) $args['in_wp'];
        $args['min_version'] = (string) $args['min_version'];

        foreach ($args as $_arg => $_value) {
            if (is_string($_value) && !isset($_value[0])) {
                throw new Exception(sprintf('Missing argument: `%1$s`.', $_arg));
            }
        } // unset($_arg, $_value); // Housekeeping.

        if ($args['in_wp']) { // It's in WordPress?
            if (!($dep_cur_version = $this->s::{'installed'.$args['type'].'Data'}($args['slug'], 'version'))) {
                throw new Exception(sprintf('Unable to acquire current version for: `%1$s`.', $args['slug']));
            } elseif (!($dep_upgrade_url = $this->upgradeUrl($args['slug'], $args['type']))) {
                throw new Exception(sprintf('Unable to generate upgrade URL for: `%1$s`.', $args['slug']));
            }
            $markup = $this->bubble; // Initialize markup w/ the bubble icon.
            $markup .= sprintf(__('<strong>%1$s is not active.</strong> It requires <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> v%4$s (or higher).', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['min_version'])).'<br />';
            $markup .= sprintf(__('You\'re running an older copy (v%1$s) of the \'%2$s\' %3$s.', 'wp-sharks-core'), esc_html($dep_cur_version), esc_html($args['name']), esc_html($args['type'])).'<br />';
            $markup .= $this->arrow.' '.sprintf(__('A simple update is necessary. <strong><a href="%1$s">Click here to upgrade %2$s</a></strong>.', 'wp-sharks-core'), esc_url($dep_upgrade_url), esc_html($args['name']));
        } else {
            $markup = $this->bubble; // Initialize markup w/ the bubble icon.
            $markup .= sprintf(__('<strong>%1$s is not active.</strong> It requires <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> v%4$s (or higher).', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['min_version'])).'<br />';
            $markup .= sprintf(__('You\'re running an older copy of the \'%1$s\' %2$s.', 'wp-sharks-core'), esc_html($args['name']), esc_html($args['type'])).'<br />';
            $markup .= $this->arrow.' '.sprintf(__('An update is necessary. <strong><a href="%1$s" target="_blank">Get the latest version of %2$s</a></strong>.', 'wp-sharks-core'), esc_url($args['url']), esc_html($args['name']));
        }
        add_action('all_admin_notices', function () use ($args, $markup) {
            global $pagenow; // Needed below.

            if (!current_user_can('update_'.$args['type'].'s')) {
                return; // Do not show.
            }
            if (in_array($pagenow, ['plugins.php', 'themes.php', 'update.php'], true)
                && ($_REQUEST['action_via'] ?? '') === $this->App->Config->©brand['©slug']) {
                return; // Not during a plugin install/activate/update action.
            }
            echo '<div class="notice notice-warning">'.
                    '<p>'.$markup.'</p>'.
                 '</div>';
        });
    }

    /**
     * Maybe enqueue dependency downgrade notice.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @param array $args Required dependency args.
     *
     * @note Intentionally choosing not to use built-in notice utilities here.
     *  The notice utilities set option values, and if we have outstanding dependencies
     *  (e.g., something triggered by a plugin hook) that could lead to unforeseen problems.
     *
     * @note Not only that, but the hooks needed to use notice utilities are not attached
     * until after a check for dependencies has been finalized; i.e., notice utils won't work anyway.
     */
    protected function maybeEnqueueDowngradeNotice(array $args)
    {
        if (!is_admin()) {
            return; // Not applicable.
        }
        $default_args = [
            'type'        => '',
            'slug'        => '',
            'name'        => '',
            'url'         => '',
            'in_wp'       => null,
            'max_version' => '',
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['type']        = (string) $args['type'];
        $args['slug']        = (string) $args['slug'];
        $args['name']        = (string) $args['name'];
        $args['url']         = (string) $args['url'];
        $args['in_wp']       = (bool) $args['in_wp'];
        $args['max_version'] = (string) $args['max_version'];

        foreach ($args as $_arg => $_value) {
            if (is_string($_value) && !isset($_value[0])) {
                throw new Exception(sprintf('Missing argument: `%1$s`.', $_arg));
            }
        } // unset($_arg, $_value); // Housekeeping.

        if ($args['in_wp']) { // It's in WordPress?
            if (!($dep_cur_version = $this->s::{'installed'.$args['type'].'Data'}($args['slug'], 'version'))) {
                throw new Exception(sprintf('Unable to acquire current version for: `%1$s`.', $args['slug']));
            } elseif (!($dep_archive_url = $this->archiveUrl($args['slug'], $args['type'], $args['max_version']))) {
                throw new Exception(sprintf('Unable to generate archive URL for: `%1$s`.', $args['slug']));
            }
            $markup = $this->bubble; // Initialize markup w/ the bubble icon.
            $markup .= sprintf(__('<strong>%1$s is not active.</strong> It requires an older version of the <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> %4$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['type'])).'<br />';
            $markup .= sprintf(__('You\'re running a newer copy (v%1$s). That will not work for %2$s, unfortunately.', 'wp-sharks-core'), esc_html($dep_cur_version), esc_html($this->App->Config->©brand['©name'])).'<br />';
            $markup .= $this->arrow.' '.sprintf(__('A manual downgrade is necessary. <strong><a href="%1$s" target="_blank">Click here to download the older v%2$s</a></strong>.', 'wp-sharks-core'), esc_url($dep_archive_url), esc_html($args['max_version'])).'<br />';
            $markup .= '<span style="display:inline-block; margin:0 0 0 1.75em;"></span>'.sprintf(__('%1$s is compatible up to %2$s v%3$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_html($args['name']), esc_html($args['max_version']));
        } else {
            $markup = $this->bubble; // Initialize markup w/ the bubble icon.
            $markup .= sprintf(__('<strong>%1$s is not active.</strong> It requires an older version of the <a href="%2$s" target="_blank" style="text-decoration:none;">%3$s</a> %4$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_url($args['url']), esc_html($args['name']), esc_html($args['type'])).'<br />';
            $markup .= sprintf(__('You\'re running a newer copy that will not work with %1$s, unfortunately.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name'])).'<br />';
            $markup .= $this->arrow.' '.sprintf(__('A manual downgrade is necessary. <strong><a href="%1$s" target="_blank">Click here to get an older version</a></strong>.', 'wp-sharks-core'), esc_url($args['url']), esc_html($args['name'])).'<br />';
            $markup .= '<span style="display:inline-block; margin:0 0 0 1.75em;"></span>'.sprintf(__('%1$s is compatible up to %2$s v%3$s.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_html($args['name']), esc_html($args['max_version']));
        }
        add_action('all_admin_notices', function () use ($args, $markup) {
            global $pagenow; // Needed below.

            if (!current_user_can('update_'.$args['type'].'s')) {
                return; // Do not show.
            }
            if (in_array($pagenow, ['plugins.php', 'themes.php', 'update.php'], true)
                && ($_REQUEST['action_via'] ?? '') === $this->App->Config->©brand['©slug']) {
                return; // Not during a plugin install/activate/update action.
            }
            echo '<div class="notice notice-warning">'.
                    '<p>'.$markup.'</p>'.
                 '</div>';
        });
    }

    /**
     * Styled arrow icon.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @type string Styled arrow icon.
     */
    protected $arrow = '<span class="dashicons dashicons-editor-break" style="-webkit-transform:scale(-1, 1); transform:scale(-1, 1);"></span>';

    /**
     * Styled bubble icon.
     *
     * @since 16xxxx Plugin/theme dependencies.
     *
     * @type string Styled bubble icon.
     */
    protected $bubble = '<span class="dashicons dashicons-admin-plugins" style="display:inline-block; width:64px; height:64px; font-size:64px; float:left; margin:-5px 10px 0 -2px;"></span>';
}
